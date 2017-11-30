/* actually there is not a module pattern */
$(document).ready(function(){
  // We need to do this because of Jquery-ui btn
  $.fn.bootstrapBtn = $.fn.button.noConflict();

  // Show a spinner while the user is waiting
  var toggleSpinner = function(text, prependTo) {
    var text = text || 'Processing';
    var prependTo = prependTo || $('body');

    // #spinner deve esistere per forza nel caso in cui lo si debba rimuovere
    if($('#spinner').length > 0) {
      $('#spinner').fadeOut('fast');
      setTimeout(function() {$('#spinner').remove()}, 400);;
    }else{
      $('<div id="spinner"><div class="loader"></div><p>' + text + '</p></div>').prependTo(prependTo);
      if(prependTo != $('body')) {
        $('#spinner').css('position', 'absolute');
        prependTo.css('position', 'relative');
      }
    }
  };

  // Refresh the whole table of check (check list page)
  var initRefreshCheckList = function() {
    toggleSpinner('', $('.checkTable').parent().parent());
    $.ajax({
      url: window.location.href,
      method: 'GET',
      success: function(response) {
        var newTable = $(response).find('.checkTable > tbody > *');
        $('.checkTable > tbody').html(newTable);
        // Re-init popover
        $('[data-toggle="popover"]').popover({html: true, trigger: 'click'});
        toggleSpinner();
      }
    });
  };

  // Show the latest error in a realtime table (dashboard)
  var initLatestEventsTable = function() {
    console.log('init table');
    toggleSpinner('', $('#latestevents').parent());
    Papa.parse('/pitbull-checker/api/logs/globalerrors', {
      download: true,
      complete: function(results){
        var csvArray = results.data;
        var name = '';
        var tbody = '';
        for (var i = csvArray.length-2; i > 1; i--) {
          var actualTimestamp = parseInt(csvArray[i][3]) * 1000;
          
          var date = new Date(actualTimestamp);
          name = csvArray[i][2];
          updown = 'down';
          if (csvArray[i][5]=='UP_AGAIN') 
            updown='up';
          tbody += '<tr>';
          tbody += '<td class="text-center"><div title="'+csvArray[i][5]+'" class="statuses tooltips status-'+csvArray[i][5]+'">';
          tbody += '<i class="glyphicon glyphicon-arrow-'+updown+'"></i>';
          tbody += '</div></td>';
          tbody += '<td>'+csvArray[i][4]+'</td>';
          tbody += '<td>'+ date.getFullYear() + "-" + (('0' + (date.getMonth()+1)).slice(-2).toString()) + "-" + (('0' + date.getDate()).slice(-2).toString()) + " " + date.getHours() + ":" + ('0'+date.getMinutes()).slice(-2).toString() + ":" + date.getSeconds() +'</td>';
          // tbody += '<td>'+csvArray[i][2]+'</td>'; // date formatted
          tbody += '<td>'+csvArray[i][5]+'</td>';
          tbody += '<td>'+csvArray[i][6]+'</td>';
          tbody += '<td>'+csvArray[i][7]+' ms</td>';
          tbody += '<td>'+csvArray[i][8]+'</td>';
          tbody += '</tr>';
        };

        if(tbody == "")
          tbody = '<td>No errors</td>';

        $('#latestevents > tbody').html(tbody);
        if(isSearchTableActive) {
          $('#searchInTable').trigger('keyup');
        }

        setTimeout(function() {
          toggleSpinner();
        }, 200);
        
      }
    });
  };

  var elaborateDataGraph = function(params) {
    Papa.parse('/pitbull-checker/logs/'+params['user']+'/'+params['user']+'.'+params['checkId']+'.csv', {
    // Papa.parse('/pitbull-checker/api/logs/partial/natural/'+ params['user'] +'/'+ params['checkId'] +'/' + params['line'], {
      download: true,
      complete: function(results) {
        // console.log(results.data);
        var csvArray = results.data;
        /* [0 => date, 1 => timestamp, 2 => name, 3 => status, 4 => status_code, 5 => response_time, 6 => message, 7 => content] */
        var dataGraphSuccess = [];
        var colors = {'': '#ffc651', 'FAILURE': '#d43f3a', 'SUCCESS': '#4cae4c', 'UP_AGAIN': '#4cae4c'};
        for (var i = 1; i < csvArray.length-1; i++) {
          if(csvArray[i][1].length == 10 && (csvArray[i][1] * 1000) > (new Date().getTime() - (86400*7*1000))) {
            dataGraphSuccess.push({
              "x": parseInt(csvArray[i][1])*1000,
              "name": csvArray[i][2],
              "y": parseFloat(csvArray[i][5]),
              "status_code" : csvArray[i][4],
              "status" : csvArray[i][3],
              "color": colors[csvArray[i][3]],
              "namelookup_time" : (csvArray[i][8]) ? parseInt(csvArray[i][8]) : '',
              "connect_time" : (csvArray[i][9]) ? parseInt(csvArray[i][9]) : '',
              "pretransfer_time" : (csvArray[i][10]) ? parseInt(csvArray[i][10]) : '',
              "starttransfer_time" : (csvArray[i][11]) ? parseInt(csvArray[i][11]) : '',
              "marker": {
                "enabled": (csvArray[i][3] != 'FAILURE') ? false : true,
                "radius": (csvArray[i][3] != 'FAILURE') ? 1 : 4
              }
            });
          }
        };

        var selectTypeGraph = '<div class="col-md-3 pull-right" style="padding-right: 0; display: none">';
        selectTypeGraph += '<select class="form-control" id="timeSelect">';
        selectTypeGraph += '<option value="">-- Select source of graph --</option>';
        selectTypeGraph += '<option value="">Total Response Time</option>';
        selectTypeGraph += '<option value="namelookup_time">NameLookup Time</option>';
        selectTypeGraph += '<option value="connect_time">Connect Time</option>';
        selectTypeGraph += '<option value="pretransfer_time">Pretransfer Time</option>';
        selectTypeGraph += '<option value="starttransfer_time">Time to First Byte</option>';
        selectTypeGraph += '</select><div class="clearfix"></div></div>';

        if(!$('#graphModal').hasClass('in')) {
          $('#graphModal .modal-body').html(selectTypeGraph+'<div id="check-graph"></div>');
          $('#graphModal .modal-title').html('History of checks for "' + params['title'] + '" &nbsp;&nbsp;&nbsp;&nbsp;' + ((params['selectValue']!='')?params['textValue']:'Total Response Time'));
          createGraph(dataGraphSuccess, [{}], params['avg']);
          toggleSpinner();
          $('#graphModal').modal();
          window.chart.rangeSelector.clickButton(0, true)
        }

        $('#timeSelect').on('change', function() {
          var actualKey = $(this).val();
          var availableGraph = ['namelookup_time', 'connect_time', 'pretransfer_time', 'starttransfer_time'];
          var newSeriesSuccess = dataGraphSuccess;
          var newSeriesFailure = [];

          if(availableGraph.indexOf(actualKey)  !== -1) {
            $('#graphModal .modal-title').find('span').remove().append('<span>'+$(this).text()+'</span>');
            for (var i = 0; i < newSeriesSuccess.length; i++) {
              if(newSeriesSuccess[i][actualKey] == '' || newSeriesSuccess[i][actualKey] == undefined){
                console.error("seriesSuccess: " + newSeriesSuccess[i][actualKey]);
                continue;
              }
              newSeriesSuccess[i]['y'] = newSeriesSuccess[i][actualKey];
            };

            // for (var i = 0; i < newSeriesFailure.length; i++) {
            //   if(newSeriesFailure[i][actualKey] == '' || newSeriesFailure[i][actualKey] == undefined) {
            //     console.error("seriesError: " + newSeriesFailure[i][actualKey]);
            //     continue;
            //   }
            //   newSeriesFailure[i]['y'] = newSeriesFailure[i][actualKey];
            // };
          }

          // console.log(window.chart);
          window.chart.series[0].setData(newSeriesSuccess, true);
          // window.chart.redraw(false);
          // window.chart.series[1].setData(newSeriesFailure, true);
        });

        $('#timeSelect').val(params['selectValue']).trigger('change');

        // // You can set a partial url
        // Papa.parse('/pitbull-checker/api/logs/nosuccess/'+ params['user'] +'/'+ params['checkId'], {
        // // Papa.parse('/pitbull-checker/api/logs/partial/natural/'+ params['user'] +'/'+ params['checkId'] +'/' + params['line'], {
        //   download: true,
        //   complete: function(results){
        //     var csvArray = results.data;
        //     var dataGraphFailure = [];
        //     for (var i = 1; i < csvArray.length-1; i++) {
        //       dataGraphFailure.push({
        //         "x": parseInt(csvArray[i][1])*1000,
        //         "name": csvArray[i][2],
        //         "y": parseFloat(csvArray[i][5]),
        //         "status_code" : csvArray[i][4],
        //         "status" : csvArray[i][3],
        //         "color": colors[csvArray[i][3]]
        //       });
        //     };

        //     var selectTypeGraph = '<div class="col-md-3 pull-right" style="padding-right: 0; display: none">';
        //     selectTypeGraph += '<select class="form-control" id="timeSelect">';
        //     selectTypeGraph += '<option value="">-- Select source of graph --</option>';
        //     selectTypeGraph += '<option value="">Total Response Time</option>';
        //     selectTypeGraph += '<option value="namelookup_time">NameLookup Time</option>';
        //     selectTypeGraph += '<option value="connect_time">Connect Time</option>';
        //     selectTypeGraph += '<option value="pretransfer_time">Pretransfer Time</option>';
        //     selectTypeGraph += '<option value="starttransfer_time">Time to First Byte</option>';
        //     selectTypeGraph += '</select><div class="clearfix"></div></div>';

        //     if(!$('#graphModal').hasClass('in')) {
        //       $('#graphModal .modal-body').html(selectTypeGraph+'<div id="check-graph"></div>');
        //       $('#graphModal .modal-title').html('History of checks for "' + params['title'] + '" &nbsp;&nbsp;&nbsp;&nbsp;' + ((params['selectValue']!='')?params['textValue']:'Total Response Time'));
        //       createGraph(dataGraphSuccess, dataGraphFailure, params['avg']);
        //       toggleSpinner();
        //       $('#graphModal').modal();
        //       window.chart.rangeSelector.clickButton(0, true)
        //     }

        //     $('#timeSelect').on('change', function() {
        //       var actualKey = $(this).val();
        //       var availableGraph = ['namelookup_time', 'connect_time', 'pretransfer_time', 'starttransfer_time'];
        //       var newSeriesSuccess = dataGraphSuccess;
        //       var newSeriesFailure = dataGraphFailure;

        //       if(availableGraph.indexOf(actualKey)  !== -1) {
        //         $('#graphModal .modal-title').find('span').remove().append('<span>'+$(this).text()+'</span>');
        //         for (var i = 0; i < newSeriesSuccess.length; i++) {
        //           if(newSeriesSuccess[i][actualKey] == '' || newSeriesSuccess[i][actualKey] == undefined){
        //             console.error("seriesSuccess: " + newSeriesSuccess[i][actualKey]);
        //             continue;
        //           }
        //           newSeriesSuccess[i]['y'] = newSeriesSuccess[i][actualKey];
        //         };

        //         for (var i = 0; i < newSeriesFailure.length; i++) {
        //           if(newSeriesFailure[i][actualKey] == '' || newSeriesFailure[i][actualKey] == undefined) {
        //             console.error("seriesError: " + newSeriesFailure[i][actualKey]);
        //             continue;
        //           }
        //           newSeriesFailure[i]['y'] = newSeriesFailure[i][actualKey];
        //         };
        //       }

        //       // console.log(window.chart);
        //       window.chart.series[0].setData(newSeriesSuccess, true);
        //       window.chart.series[1].setData(newSeriesFailure, true);
        //     });

        //     $('#timeSelect').val(params['selectValue']).trigger('change');

        //   }
        // });
      }
    });
  };

  // I need a module pattern here..
  var app = {
    config: {
      basePath: '/pitbull-checker'
    },
    createGraph: function(series1, series2, avg) {}
  };

  // We don't use this, but it's ready..
  $(".submenu > a").click(function(e) {
    e.preventDefault();
    var $li = $(this).parent("li");
    var $ul = $(this).next("ul");

    if($li.hasClass("open")) {
      $ul.slideUp(350);
      $li.removeClass("open");
    } else {
      $(".nav > li > ul").slideUp(350);
      $(".nav > li").removeClass("open");
      $ul.slideDown(350);
      $li.addClass("open");
    }
  });

  // Build jQuery Knobs
  if($('.knob').length)
    $(".knob").knob();

  // Build tags plugin
  if($('.tags').length)
    $('.tags').tagsInput();

  var isSearchTableActive = false;
  $('body').on('keyup', '#searchInTable', function(e){
    var actualEl = $(this);
      setTimeout(function(){
        var tableTarget = $(actualEl.attr('data-table'));
        var actualVal = actualEl.val().trim();
        isSearchTableActive = (actualVal != "") ? true : false;

        tableTarget.find('tr').each(function(){
          var foundARecordInTable = false;
          $(this).find('td').each(function() {
            var regXp = new RegExp(actualVal.toLowerCase(), 'gi');
            console.log($(this).text().trim().toLowerCase() +' - ' + actualVal);
            if($(this).text().trim().toLowerCase().search(regXp) !== -1) {
              foundARecordInTable = true;
              return true;
            }
          });

          if(foundARecordInTable) {
            $(this).show();
          }else{
            $(this).hide();
          }

        });
    }, 200);
  });

  // Bulk operation in check list
  $('body').on('click', '.updateBtn', function(e) {
    e.preventDefault();
    var actions = ['enable', 'disable', 'changefrequency', 'changemaxerrors', 'reseterrors']
    var selectVal = $('#selectAction').val();

    if(!$('.row-select:checked').length) {
      alert('You need to select at least one check');
      return false;
    }

    if(actions.indexOf(selectVal) !== -1) {
      var checks = [];
      var user = "";
      $('.row-select').each(function() {
        if($(this).prop('checked')) {
          user = $(this).attr('data-user');
          checks.push($(this).attr('data-checkid'));
        }
      });

      var promptValue = '';

      if(selectVal != 'enable' && selectVal != 'disable') {
        if(promptValue = '/'+prompt('Insert a value:')) {
          updateChecks(selectVal, user, promptValue, checks);
        }
        return true;
      }

      updateChecks(selectVal, user, promptValue, checks);

    }else{
      alert('Select an action');
    }
  });

  // Not used anymore => use bulk operations btn instead
  $('body').on('click', '.glyphicon-stop, glyphicon-play', function(e) {
    e.preventDefault();
    var checkId = $(this).attr('data-checkid');
    var sameUser = $(this).attr('data-user');
    var statusToSet = ($(this).hasClass('glyphicon-play')) ? 'active' : 'disabled';
    var el = $(this);
    alert("Da verificare il salvataggio parziale.");
    // $.ajax({
    //   url: '/pitbull-checker/app/edit/' + checkId,
    //   method: 'POST',
    //   data: {status: statusToSet, user: sameUser, id: checkId},
    //   success: function(response) {
    //     if(!response['error']) {
    //       var classToSet = (statusToSet=='active') ? 'glyphicon-stop' : 'glyphicon-play';
    //       el.removeClass('glyphicon-stop').removeClass('glyphicon-play').addClass(classToSet)
    //     }else{
    //       alert(response['error']);
    //     }
    //   }
    // });

  });

  var actualElement = "";
  var actualParams = {};
  $('#graphModal').on('hidden.bs.modal', function() {
    $('body').find('.graph-active').removeClass('graph-active');
    actualElement = "";
    actualParams = {};
  });

  /* open the log graph for the check */
  $('body').on('click', '.viewGraph', function(e, line) {
    e.preventDefault();

    if(actualElement == ""){
      actualElement = $(this);
      toggleSpinner();
    }

    var selectValue = $(this).parent().parent().find('.timeSelect').val();
    var textValue = $(this).parent().parent().find('.timeSelect').find(':selected').text();
    var line = line || 15;

    var checkId = $(this).attr('data-id');
    var user = $(this).attr('data-user');
    var title = $(this).attr('data-title');
    var avg = $(this).attr('data-avg');

    actualParams = {
      checkId: checkId,
      user: user,
      title: title,
      avg: avg,
      line: line,
      selectValue: selectValue,
      textValue: textValue
    };

    elaborateDataGraph(actualParams);

  });

  $('body').on('hidden.bs.modal', function() {
    $('#transfertime-chart').dialog('close');
  });

  // Init dynamic tooltips
  $('body').tooltip({
      selector: '.tooltips'
  });

  // After a popover is clicked, toggle the other that are still active
  $('body').on('click', function (e) {
      $('[data-toggle="popover"]').each(function () {
          //the 'is' for buttons that trigger popups
          //the 'has' for icons within a button that triggers a popup
          if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
              $(this).popover('hide');
              $(this).parent().find('.popover').remove();
          }
      });
  });

  // Init dynamic popover
  // $('body').popover({selector: '[data-toggle="popover"]', html: true, trigger: 'click', content: function () {
  //       return $(this).attr('data-content');
  //   }});

  $('[data-toggle="popover"]').popover({html: true, trigger: 'click'});


  // Open the table errors per check
  $('body').on('click', '.openTable', function(e) {
    e.preventDefault();
    var user = $(this).attr('data-user');
    var checkId = $(this).attr('data-id');
    Papa.parse('/pitbull-checker/api/logs/errors/'+user+'/'+checkId, {
      download: true,
      complete: function(results){
        var csvArray = results.data;
        var name = '';
        var tbody = '';
        for (var i = csvArray.length-2; i > 1; i--) {
          var actualTimestamp = parseInt(csvArray[i][1]) * 1000;
          var date = new Date(actualTimestamp);

          name = csvArray[i][2];
          updown = 'down';
          if (csvArray[i][3]=='UP_AGAIN') 
            updown='up';
          tbody += '<tr>';
          tbody += '<td><div title="'+csvArray[i][3]+'" class="statuses tooltips status-'+csvArray[i][3]+'">';
          tbody += '<i class="glyphicon glyphicon-arrow-'+updown+'"></i>';
          tbody += '</div></td>';
          tbody += '<td>'+ date.getFullYear() + "-" + (('0' + (date.getMonth()+1)).slice(-2).toString()) + "-" + (('0' + date.getDate()).slice(-2).toString()) + " " + date.getHours() + ":" + ('0'+date.getMinutes()).slice(-2).toString() + ":" + date.getSeconds() +'</td>';
          // tbody += '<td>'+csvArray[i][0]+'</td>';
          tbody += '<td>'+csvArray[i][3]+'</td>';
          tbody += '<td>'+csvArray[i][4]+'</td>';
          tbody += '<td>'+csvArray[i][5]+' ms</td>';
          tbody += '<td>'+csvArray[i][6]+'</td>';
          tbody += '</tr>';
        };

        if(tbody == "")
          tbody = '<td>No errors</td>';

        $('#errorModal .modal-title').html('History of errors for ' + name);
        $('#errorModal .modal-body tbody').html(tbody);
        $('#errorModal').modal();

      }
    });
  });
  
  // Init the global last errors table in dashboard (or wherever exists #latestevents)
  if($('#latestevents').length) {
    initLatestEventsTable();
    setInterval(initLatestEventsTable, 60000);

  }

  // If we are on checklist, refresh table
  if($('.checkTable').length) {
    setInterval(initRefreshCheckList, 60000);
  }

  $('body').on('change', '.success_criteria_type', function(e) {
    // e.preventDefault();
    var operation = $(this).find(':selected').attr('data-operation');
    $(this).parent().parent().find('.input-group-addon').eq(0).text(operation);
  });

  $('.success_criteria_type').trigger('change');

  // da inserire l'aggiunta dinamica in settings di telegram email e sms
  // idem in check, posso usare lo stesso
  $('body').on('click', '.addCriteria', function(e) {
    e.preventDefault();

    console.log('clicked');
    var containerID = $(this).attr('data-parent-id');
    var clone = $('#'+containerID+' .form-group').last().clone(true);
    clone.find('select, input').each(function(index) {
      var oldname = $(this).attr('name');
      var total = $('#'+containerID+' .form-group').length;
      var newname = oldname.replace(/\[\d+\]/, '['+total+']');
      $(this).attr('name', newname);
    });
    $('#'+containerID).append(clone);

  });

  $('body').on('click', '.addParams', function(e) {
    e.preventDefault();

    console.log('clicked');
    var containerID = $(this).attr('data-parent-id');
    var clone = $('#'+containerID+' .form-group').last().clone(true);
    clone.find('input').each(function(index) {
      $(this).attr('name', '');
      $(this).val("");
    });
    // clone.find('.removeRow').parent().css('display', 'block');
    $('#'+containerID).append(clone);
  });

  $('body').on('change', '#request-method', function(e) {
    if($(this).val() == 'POST')
      $('#params-container').show();
    else
      $('#params-container').hide();
  });

  $('body').on('click', '.openPopover', function(e) {
    e.preventDefault();
  });

  $('body').on('keyup', '.paramsKey', function(e) {
    var el = $(this).parent().parent().find('.paramsValue');
    var key = $(this).val();
    setTimeout(function() {
      el.attr('name', 'check[form_params]['+key+']');
    }, 200);
  });

  $('body').on('click', '.addNotify', function(e) {
    e.preventDefault();

    console.log('clicked notify');
    appendChannel();

    var channels = JSON.parse($(this).attr('data-user'));
    var elChannel = $('.channelList').last();
    var elAddress = $('.channelValue').last();

    elChannel.html("<option value=''>-- select a channel --</option>");
    elAddress.html("<option class='default-option' value=''>-- select an address --</option>");

    for (var key in channels) {
      elChannel.append('<option value="'+key+'">'+key+'</option>');
      for (var i = 0; i < channels[key].length; i++) {
        elAddress.append('<option class="'+key+'" style="display: none" value="'+channels[key][i]+'">'+channels[key][i]+'</option>');
      };
    }

    // $('#dialog-notify').modal('show');
    //$('.saveNotify').trigger('click');

  });

  $('body').on('change', '#channelList, .channelList', function(e) {
    var actualVal = $(this).val();
    $(this).parent().parent().find('.channelValue').trigger('change');
    $(this).parent().parent().find('.channelValue option').css('display', 'none');
    $(this).parent().parent().find('.channelValue .default-option').css('display', 'block').prop('selected', 'selected');
    $(this).parent().parent().find('.channelValue .'+actualVal).css('display', 'block');
    $(this).parent().parent().find('.channelValue').attr('name', 'alert['+actualVal+'][]');
  });

  $('body').on('click', '.saveNotify', function(e){
    // var channel = $('#channelList').val();
    // var value = $('#channelValue').val();

    var html = '<div class="form-group">';
    html += '<div class="col-md-3 col-md-push-3">';
    html += '<select class="form-control channelList" name="alert['+channel+']">'+$('#channelList').html()+'</select>';
    html += '</div>';
    html += '<div class="col-md-3 col-md-push-3">';
    html += '<select class="form-control channelValue" name="alert['+channel+'][]">'+$('#channelValue').html()+'</select>';
    html += '</div>';
    html += '<div class="col-md-1 col-md-push-3 text-left">';
    html += '<i class="glyphicon glyphicon-trash removeRow tooltips" title="remove channel"></i>';
    html += '</div>';
    html += '</div>';
    html += '<div class="clearfix"></div>';
    html += '<br/>';

    $('#alert-container').append(html);

  });

  $('body').on('click', '.removeRow', function(e) {
    if(confirm("This operation will remove the whole row. Do you want to continue?")) {
      $(this).parent().parent().remove();
    }
  });  


  // Show only a type of graph
  $('.showCurrentGraph').on('click', function(e) {
    $(this).hide();
    var user = $(this).attr('data-user');
    var checkId = $(this).attr('data-checkid');
    $('#hero-graph-'+checkId).show();
    Papa.parse('/pitbull-checker/api/logs/partial/'+user+'/'+checkId, {
      download: true,
      complete: function(results){
        // Morris Line Chart
        if(results.data.length) {
          var graphData = [];
          for (var i = 0; i < results.data.length-1; i++) {
            graphData.push({
              'period': results.data[i][0],
              'response_time': results.data[i][5],
              'color': ((results.data[i][3] == 'SUCCESS') ? 'green': 'red')
            });
          };

          // console.log(graphData);
          Morris.Line({
              element: 'hero-graph-'+checkId,
              data: graphData,
              xkey: 'period',
              xLabels: "Time",
              // lineColors: ['red', 'green']
              ykeys: ['response_time'],
              labels: ['Tot. Response Time']
          });
        }
      }
    });

  });

  function updateChecks(selectVal, user, promptValue, checks) {
    $.ajax({
      url: '/pitbull-checker/api/bulk/'+selectVal+'/'+user+promptValue,
      method: 'POST',
      data: {
        checks: checks,
        user: user
      },
      success: function(response) {
        if(response.success) {
          alert('Action completed!');
          setTimeout(function() { window.location.reload()}, 1500);
        }else{
          alert(response['error']);
        }
      }
    });
  }


  // setInterval(function() {
  //   updateCheckList();
  // }, 5000);

  function appendChannel() {
    var channel = $('#channelList').val();
    var value = $('#channelValue').val();

    var html = '<div class="form-group">';
    html += '<div class="col-md-3 col-md-push-3">';
    html += '<select class="form-control channelList">'+$('#channelList').html()+'</select>';
    html += '</div>';
    html += '<div class="col-md-3 col-md-push-3">';
    html += '<select class="form-control channelValue" name="alert['+channel+'][]">'+$('#channelValue').html()+'</select>';
    html += '</div>';
    html += '<div class="col-md-1 col-md-push-3 text-left">';
    html += '<i class="glyphicon glyphicon-trash removeRow tooltips" title="remove channel"></i>';
    html += '</div>';
    html += '</div>';
    html += '<div class="clearfix"></div>';
    html += '<br/>';

    $('#alert-container').append(html);
  }


  // function updateCheckList() {
  //   return true;
  //   var count = 0;
  //   $('.edit-btn').each(function() {
  //     var checkid = $(this).attr('data-id');
  //     var row = $(this).parent().parent();
  //     var needCheck = ($(this).attr('data-next-check')) ? parseInt($(this).attr('data-next-check')) : new Date().getTime();

  //     if(needCheck < new Date().getTime() && count < 5) {
  //       count++;      
  //       $.ajax({
  //         method: 'post',
  //         url: '',
  //         success: function(response) {
  //           if(response['last_check']['status']) {
  //             var dateTimestamp = new Date(response['last_check']['timestamp']);
  //             var newText = "Date: " + dateTimestamp.getFullYear() "-" + (dateTimestamp.getMonth()+1) + "-" + dateTimestamp.getDate() + " " + dateTimestamp.getHours()+1 + ":" + dateTimestamp.getMinutes() + ":" +dateTimestamp.getSeconds() + " - Response Time: "+ response['last_check']['time'] +" - Response: " + response['last_check']['statuscode'];
  //             row.find('.statuses').attr('class', 'statuses tooltips status-'+response['last_check']['status']);
  //             row.find('small').attr('data-original-title', response['last_check']['message']);
  //             row.find('small').text(newText);
  //           }
  //         }
  //       });
  //     }

  //   });
  // }

  // Not in use
  function calcAvg(dataGraphSuccess) {
    var count = dataGraphSuccess.length;
    var sum = 0;
    var avg = 0;

    if(count){
      for (var i = 0; i < count; i++) {
        sum = sum + dataGraphSuccess[i]['y'];
      };

      avg = sum / count;
    }

    return parseInt(avg);
  }


  function calcPercGraph(a, b) {
    var limit = 40; // parameters
    var k= 10; // parameters

    var percent = parseInt((a / b) * 100);
    if(percent >= limit && k != 0) {
      percent -= k;
    }

    return percent;
  }

  function createGraph(dataGraphSuccess, dataGraphFailure, avg){

    Highcharts.setOptions({
      global: {
          timezoneOffset: -60
      }
    });

    window.chart = Highcharts.stockChart('check-graph', {
      chart: {
        // type: 'spline',
        zoomType: 'x',
        panning: true,
        panKey: 'shift',
        events:{
          addSeries: function(e) {
            console.log('added series');
          },
          redraw: function(e) {
            toggleSpinner('', $('#check-graph').parent());
            console.log('redraw');
          },
          render: function(e) {
            console.log('render');
            toggleSpinner();
          }
        }
      },
      // title: {
      //   text: 'Response request in ms by time'
      // },

      yAxis: {
        title: 'millisecond',
        // type: '',
        plotLines: [{
          color: '#FF0000',
          width: 2,
          value: avg,
          zIndex: 9,
          label: {text: "Total Response time (avg): " + avg + ' ms', y: 20, style: {fontWeight: "bold"}}
        }]
      },

      xAxis: {
        title: 'Time',
        type: 'datetime',
        ordinal: true,
        crosshair: {
          snap: false
        },
        events: {
         setExtremes: function(e) {
          // console.log(e.rangeSelectorButton.text);
          var availableButton = {
            "15 Minutes": 15,
            "1 Hour": 60,
            "6 Hours": (60*60*6)/60,
            "24 Hours": (60*60*24)/60,
            "48 Hours": (60*60*24*2)/60,
            "Last Week": (60*60*24*7)/60,
          };
          if(e.rangeSelectorButton !== undefined && availableButton[e.rangeSelectorButton.text] !== undefined) {
            console.log(availableButton[e.rangeSelectorButton.text]);
            console.log(actualElement);
            // actualElement.trigger('click', [availableButton[e.rangeSelectorButton.text]]);
            actualParams['line'] = availableButton[e.rangeSelectorButton.text];
            // elaborateDataGraph(actualParams);
            // var newSeriesSuccess = dataGraphSuccess;
            // var newSeriesFailure = dataGraphFailure;

            // if(availableGraph.indexOf(actualKey)  !== -1) {
            //   $('#graphModal .modal-title').find('span').remove().append('<span>'+$(this).text()+'</span>');
            //   for (var i = 0; i < newSeriesSuccess.length; i++) {
            //     if(newSeriesSuccess[i][actualKey] == '') continue;
            //     newSeriesSuccess[i]['y'] = newSeriesSuccess[i][actualKey];
            //   };

            //   for (var i = 0; i < newSeriesFailure.length; i++) {
            //     if(newSeriesFailure[i][actualKey] == '') continue;
            //     newSeriesFailure[i]['y'] = newSeriesFailure[i][actualKey];
            //   };
            // }

            // console.log(newSeriesSuccess);
            // window.chart.series[0].setData(newSeriesSuccess, true);
            // window.chart.series[1].setData(newSeriesFailure, true);
          }

         }
        }
      },

      boost: {
        useGPUTranslations: true
      },

      rangeSelector: {
        inputEnabled: false,
        allButtonsEnabled: true,
        // selected: 0,
        buttons: [{
            type: 'minute',
            count: 15,
            text: '15 Minutes',
            dataGrouping: {
                forced: true,
                units: [['minute', [1]]]
            }
        }, {
            type: 'hour',
            count: 1,
            text: '1 Hour',
            dataGrouping: {
                forced: true,
                units: [['minute', [1]]]
                // units: [['minute', [1, 2, 3, 5, 10, 20, 25, 30, 60, 100]]]
            }
        }, {
            type: 'hour',
            count: 6,
            text: '6 Hours',
            dataGrouping: {
                forced: true,
                units: [['minute', [1]]]
            }
        }, {
            type: 'day',
            count: 1,
            text: '24 Hours',
            dataGrouping: {
                forced: true,
                units: [['minute', [2]]]
          }
        }, {
            type: 'day',
            count: 2,
            text: '48 Hours',
            dataGrouping: {
                forced: true,
                units: [['minute', [2]]]
          }
        }, {
            type: 'week',
            count: 1,
            text: 'Last Week',
            dataGrouping: {
                forced: true,
                units: [['minute', [10]]]
            }
        }],
        buttonTheme: {
          width: 60
        }
      },

      tooltip: {
        shared: false,
          formatter: function() {
            var time = new Date(this.x);
            return time.getFullYear()+'-'+('0' + (time.getMonth()+1)).slice(-2).toString()+'-'+('0' + time.getDate()).slice(-2).toString() + " " + ('0'+time.getHours()).slice(-2).toString() + ":" + ('0'+time.getMinutes()).slice(-2).toString() + " " + this.points[0].point.status + ' Response Time: '+ parseInt(this.y) + " ms";
             // +'ms - Status: '+ this.points[0].point.status + ' - Code: ' + this.points[0].point.status_code;
          }
      },

      navigator: {
        enabled: false,
        adaptToUpdatedData: false
      },

      series: [{
        name: " ",
        boostThreshold: 400,
        turboThreshold: 0,
        data: dataGraphSuccess,
        lineWidth: 1,
        shadow: false,
        type: 'spline',
        dataGrouping: {
          enabled: false
        },
        marker: {},
        cursor: 'pointer',
        point: {
          events: {
            click: function () {
              // alert('Category: ' + this.x + ', value: ' + this.y);
              if($('#timeSelect').val() != "")
                return false;
              
              var html = '';

              connectTime = this.options.connect_time - this.options.namelookup_time;
              preTransferTime = this.options.pretransfer_time - this.options.connect_time;
              startTransferTime = this.options.starttransfer_time - this.options.pretransfer_time;
              transferTime = this.y - this.options.starttransfer_time;

              var width = {
                  'namelookup_time' : calcPercGraph(this.options.namelookup_time, this.y),
                  'connect_time' : calcPercGraph(connectTime, this.y),
                  'pretransfer_time' : calcPercGraph(preTransferTime, this.y),
                  'starttransfer_time' : calcPercGraph(startTransferTime, this.y),
                  'transfer_time' : calcPercGraph(transferTime, this.y)
                };

              var totalWidth = 0;
              for(var key in width) {
                totalWidth += width[key];
              }

              var i = 0;
              while(totalWidth < 100 && i < 15) {
                for (var key in width){
                  if(width[key] < 10){
                    width[key]++;
                    totalWidth++;
                    break;
                  }
                }
                i++;
              }

              html += '<div id="transfertime-chart"><div class="progress" style="width: 100%; margin-top: 30px">';
              html += '<div class="progress-bar progress-bar-warning tooltips" title="Name Lookup" role="progressbar" style="width: '+width['namelookup_time']+'%">';
              html += this.options.namelookup_time;
              html += '</div>';
              html += '<div class="progress-bar progress-bar-danger tooltips" title="Connect" role="progressbar" style="width: '+width['connect_time']+'%">';
              html += connectTime;
              html += '</div>';
              html += '<div class="progress-bar progress-bar-success tooltips" title="PreTransfer" role="progressbar" style="width: '+width['pretransfer_time']+'%">';
              html += preTransferTime;
              html += '</div>';
              html += '<div class="progress-bar progress-bar-info tooltips" title="StartTransfer (TTFB)" role="progressbar" style="width: '+width['starttransfer_time']+'%">';
              html += startTransferTime;
              html += '</div>';
              html += '<div class="progress-bar progress-bar-success tooltips" title="TransferTime" role="progressbar" style="width: '+width['transfer_time']+'%">';
              html += transferTime;
              html += '</div>';
              html += '</div>';
              html += '</div>';

              $('#transfertime-chart').dialog('close').remove();
              $(html).dialog({
                  title: 'Total transfer time: ' + this.y + ' ms',
                  width: 400,
                  height: 130,
                  position: {
                    my: 'center',
                    at: 'center',
                    of: $('#check-graph')
                  }
              });
            }
          }
        }
      },
      // {
      //   name: "  ",
      //   turboThreshold: 0,
      //   data: dataGraphFailure,
      //   type: '',
      //   lineWidth: 0,
      //   marker: {
      //     enabled: true,
      //     radius: 4
      //   },
      //   states: {
      //     hover: {
      //       lineWidthPlus: 0
      //     }
      //   },
      //   dataGrouping: {
      //     enabled: false
      //   }
      // }
      ]
    });

  }

});