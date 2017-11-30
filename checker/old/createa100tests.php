<?php


$config = json_decode(file_get_contents('/home/pi/www/pitbullcheck/config/checks/template/aperion.dasded3.json.template'),true);

$checks=[
"https://youtoyoutuber.com",
"https://www.amazon.com/stream/e2813fb6-c216-41e7-af4f-e00bd320533d/ref=strm_theme_mens",
"https://theoutline.com",
"https://theculturetrip.com",
"https://www.seeker.com/",
"https://medium.com/the-mission",
"https://trends.google.com/trends/",
"https://thoughteconomics.com/",
"http://www.micrositemasters.com/blog/penguin-analysis-seo-isnt-dead-but-you-need-to-act-smarter-and-5-easy-ways-to-do-so/",
"http://trendwatching.com",
"https://betalist.com",
"https://www.boxed.com",
"https://www.amazon.com/b/ref=sv_lp_4?ie=UTF8&node=12034491011",
"https://www.eater.com",
"https://www.loc.gov",
"https://www.squaremile.com",
"https://www.itsnicethat.com",
"https://futurism.com",
"https://kinfolk.com",
"http://www.mini-magazine.com",
"https://www.google.com/culturalinstitute/beta/?hl=en",
"http://www.orderofman.com",
"https://www.fatherly.com",
"https://kottke.org",
"http://silesoleil.com",
"https://www.morethanjustparks.com",
"http://www.comune.magenta.mi.it/",
"https://readcereal.com/",
"http://www.lonny.com",
"https://www.poets.org",
"https://www.uopeople.edu/",
"http://www.marketwatch.com/entertainment?reflink=heatst",
"http://www.vogue.it/news/notizie-del-giorno/2017/10/05/oh-you-pretty-things-manifesto-vogue-italia-ottobre-2017/",
"http://www.aperion.it",
"http://trottermag.com",
"https://www.theringer.com/",
"https://www.wareable.com",
"https://www.booooooom.com",
"https://shop-generalstore.com",
"http://www.dailyoverview.com",
"http://thechalkboardmag.com",
"https://www.dessertfortwo.com/",
"https://howwegettonext.com/who-are-we-e880f45b48c3",
"https://searchenginewatch.com/sew/news/2172839/google-penguin-update-impact-anchor-text-diversity-link-relevancy",
"http://www.extracrispy.com",
"http://www.lavinlabel.com",
"https://www.inverse.com",
"http://coolmaterial.com",
"https://medium.com/personal-growth",
"https://foodstirs.com",
"http://www.citizensoftheworld.cc",
"https://mocoloco.com",
"https://hackernoon.com/what-habits-made-me-a-better-software-engineer-47e7d54b8fa",
"https://www.uplabs.com",
"https://www.futurelearn.com",
"https://betterhumans.coach.me/cant-kick-a-bad-habit-you-re-probably-doing-it-wrong-95ef1e0c2851",
"https://www.thebillfold.com/",
"https://muz.li",
"https://thebrowser.com",
"https://www.outofprintclothing.com",
"https://letterlist.com/",
"http://nymag.com/scienceofus/",
"http://www.lostateminor.com",
"https://nextshark.com/",
"https://www.thelostavocado.com/",
"http://www.handmadecharlotte.com",
"https://bensbargains.com/",
"https://courses.lumenlearning.com/catalog/boundlesscourses",
"http://time.com/photography/lightbox/",
"http://darkroom.baltimoresun.com",
"https://www.racked.com/",
"https://brightreads.com/why-every-teen-should-hike-3ebe4374dc23",
"http://weandthecolor.com",
"https://herschel.com/",
"https://uploadvr.com",
"http://www.homedsgn.com",
"http://www.fashionbeans.com",
"https://thecoffeelicious.com/gps-destination-death-85b175d24038",
"http://arkitip.com",
"http://www.frozenlemons.com/index.html",
"http://thephotosociety.org",
"http://www.yopk.gr",
"http://www.backdownsouth.com",
"http://www.ladolcevitablog.com",
"http://edu.inc.com",
"https://www.alpinemodern.com/shop/",
"https://meh.com",
"https://hackaday.com/",
"http://sewcaroline.com",
"https://www.yatzer.com",
"http://thekentuckygent.com",
"https://www.astormtrooperaday.com",
"https://thesilphroad.com",
"http://tinytimes.com",
"http://resourcemagonline.com",
"http://www.nitch.com",
"https://ohmy.disney.com",
"https://www.camelsandchocolate.com",
"https://www.goodthingsguy.com",
"https://uniquehunters.com",
"https://www.solidsmack.com"];

// ecco cosa devo cambiare:
foreach ($checks as $value) {
	$new=$config;
	$tochange=[
		"id"=>uniqid(),
		"name"=>$value,
		"frequency"=>1,
		"url"=>$value,
		"user"=>"thinkermail"
	];
	$new['id']=$tochange['id'];
	$new['name']=$tochange['name'];
	$new['frequency']=$tochange['frequency'];
	$new['user']=$tochange['user'];
	$new['check']['url']=$tochange['url'];
	$new['check']['success_criteria']=array($config['check']['success_criteria'][0],$config['check']['success_criteria'][1]);
	
	//print_r($new);
	file_put_contents('/home/pi/www/pitbullcheck/config/checks/thinkermail.'.$new['id'].'.json', json_encode($new,JSON_PRETTY_PRINT));
	echo "salvato file....".'aperion.'.$new['id'].'.json'."\n";
}



