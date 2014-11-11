<?php 
	$ih = Loader::helper("image");
	$nav = Loader::helper("navigation");
	foreach($items as $item){ 
		if($item['pageID']){
			//if set, grab the page object.
			$page = Page::getByID($item['pageID']);
			$pageName = $page->getCollectionName();
			$theLink = $nav->getLinkToCollection($page);
		}
		if($item['fID']){
			//if set, grab the file object
			$fileObj = File::getByID($item['fID']);
			//thumbnail settings.
			$width = 300;
			$height = 180;
			$crop = true;
			$thumb = $ih->getThumbnail($fileObj,$width,$height,$altText,$crop);
		}
?>
    <h1><?=$item['title']?></h1>
    <p><?=$item['opt']?></p>
    <?=$item['wysiwyg']?>
    <?php if($item['fID']){?>
    	<img src="<?=$thumb->src?>">
    <?php } ?>
    <p><a href="<?=$theLink?>"><?=$pageName?></a></p>
    
<?php } ?>