<?php
defined('C5_EXECUTE') or die("Access Denied.");
// LOAD FOR PAGE SELECTOR
$pageSelector = Loader::helper('form/page_selector');

// LOAD FOR REDACTOR & FILE SELECTOR
$fp = FilePermissions::getGlobal();
$tp = new TaskPermission(); ?>
<style type="text/css">
    .redactor_editor { padding: 20px; }
</style>

<!-- USE FOR IMAGE SELECTOR -->
<style type="text/css">
.select-image { display: block; padding: 15px; cursor: pointer; background: #dedede; border: 1px solid #cdcdcd; text-align: center; color: #333; vertical-align: center; }
.select-image img { max-width: 100%; }
</style>


<style type="text/css">
	.panel-heading { cursor: move; }
    .panel-body { display: none; }
</style>

<div class="well bg-info">
    <?php echo t('You can rearrange items if needed.'); ?>
</div>

<div class="items-container">
    
    <!-- DYNAMIC ITEMS WILL GET LOADED INTO HERE -->
    
</div>  

<span class="btn btn-success btn-add-item"><?php echo t('Add Item') ?></span> 


<!-- THE TEMPLATE WE'LL USE FOR EACH ITEM -->
<script type="text/template" id="item-template">
    <div class="item panel panel-default" data-order="<%=sort%>">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-6">
                    <h5><i class="fa fa-arrows drag-handle"></i>
                    Item <%=parseInt(sort)+1%></h5>
                </div>
                <div class="col-xs-6 text-right">
                    <a href="javascript:editItem(<%=sort%>);" class="btn btn-edit-item btn-default"><?=t('Edit')?></a>
                    <a href="javascript:deleteItem(<%=sort%>)" class="btn btn-delete-item btn-danger"><?=t('Delete')?></a>
                </div>
            </div>
        </div>
        <div class="panel-body form-horizontal">
            <div class="form-group">
                <label class="col-xs-3 control-label" for="title<%=sort%>"><?=t('Title:')?></label>
                <div class="col-xs-9">
                	<input class="form-control" type="text" name="title[]" id="title<%=sort%>" value="<%=title%>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label" for="opt<%=sort%>"><?=t('Option Question:')?></label>
                <div class="col-xs-9">
	                <select class="form-control" name="opt[]" id="opt<%=sort%>">
	                    <option value="birds" <%= opt=='birds' ? 'selected' : '' %>><?=t('I Love Birds')?></option>
	                    <option value="bees" <%= opt=='bees' ? 'selected' : '' %>><?=t('Bee Sting')?></option>
	                    <option value="bananas" <%= opt=='bananas' ? 'selected' : '' %>><?=t('Bananas')?></option>
	                    <option value="apples" <%= opt=='apples' ? 'selected' : '' %>><?=t('Apples')?></option>
	                </select>
                </div>
            </div>
            
            <!-- REDACTOR --->
            <div class="form-group">
                <label class="col-xs-3 control-label" for="wysiwyg<%=sort%>"><?=t('Put in some Content:')?></label>
                <div class="col-xs-9">
                	<textarea class="redactor-content" name="wysiwyg[]" id="wysiwyg<%=sort%>"><%=wysiwyg%></textarea>
				</div>
            </div>
            
            <!-- IMAGE SELECTOR --->
            <div class="form-group">
	            <label class="col-xs-3 control-label"><?php echo t('Select Image') ?></label>
	            <div class="col-xs-9">
		            <a href="javascript:chooseImage(<%=sort%>);" class="select-image" id="select-image-<%=sort%>">
		                <% if (thumb.length > 0) { %>
		                    <img src="<%= thumb %>" />
		                <% } else { %>
		                    <i class="fa fa-picture-o"></i>
		                <% } %>
		            </a>
		            <input type="hidden" name="<?php echo $view->field('fID')?>[]" class="image-fID" value="<%=fID%>" />
	            </div>
	        </div>
                        
            <!-- PAGE SELECTOR --->
            <div class="form-group">
                <label class="col-xs-3 control-label"><?=t('Select a Page')?></label>
                <div class="col-xs-9" id="select-page-<%=sort%>">
					<?php $this->inc('elements/page_selector.php');?>
				</div>
            </div>
            
            <input class="item-sort" type="hidden" name="<?php echo $view->field('sort')?>[]" value="<%=sort%>"/>
            
        </div>
    </div><!-- .item -->
</script>


<script type="text/javascript">

//Edit Button
var editItem = function(i){
	$(".item[data-order='"+i+"']").find(".panel-body").toggle();
};
//Delete Button
var deleteItem = function(i) {
    var confirmDelete = confirm('<?php echo t('Are you sure?') ?>');
    if(confirmDelete == true) {
        $(".item[data-order='"+i+"']").remove();
        indexItems();
    }
};
//Choose Image
var chooseImage = function(i){
	var imgShell = $('#select-image-'+i);
    ConcreteFileManager.launchDialog(function (data) {
        ConcreteFileManager.getFileDetails(data.fID, function(r) {
            jQuery.fn.dialog.hideLoader();
            var file = r.files[0];
            imgShell.html(file.resultsThumbnailImg);
            imgShell.next('.image-fID').val(file.fID)
        });
    });
};

//Index our Items
function indexItems(){
    $('.items-container .item').each(function(i) {
        $(this).find('.item-sort').val(i);
        $(this).attr("data-order",i);
    });
};

$(function(){
    
    //DEFINE VARS
    
        //use when using Redactor (wysiwyg)
        var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Loader::helper('validation/token')->generate('editor')?>";
        
        //Define container and items
        var itemsContainer = $('.items-container');
        var itemTemplate = _.template($('#item-template').html());
    
    //BASIC FUNCTIONS
    
        //Make items sortable. If we re-sort them, re-index them.
        $(".items-container").sortable({
            handle: ".panel-heading",
            update: function(){
                indexItems();
            }
        });
    
    //LOAD UP OUR ITEMS
        
        //for each Item, apply the template.
        <?php 
        if($items) {
            foreach ($items as $item) { 
        ?>
        itemsContainer.append(itemTemplate({
            //define variables to pass to the template.
            title: '<?php echo addslashes($item['title']) ?>',
            opt: '<?php echo addslashes($item['opt']) ?>',
            
            //REDACTOR
            wysiwyg: '<?php echo str_replace(array("\t", "\r", "\n"), "", addslashes($item['wysiwyg']))?>',
            
            //IMAGE SELECTOR
            fID: '<?php echo $item['fID'] ?>',
            <?php if($item['fID']) { ?>
            thumb: '<?php echo File::getByID($item['fID'])->getThumbnailURL('file_manager_listing');?>',
            <?php } else { ?>
            thumb: '',
			<?php } ?>
			
			//PAGE SELECTOR
			<?php if($item['pageID']){
				$page = Page::getByID($item['pageID']);
				$pageName = $page->getCollectionName();
			}
			?>
			pageID: '<?=$item['pageID']?>',
			pageName: '<?=$pageName?>',
			
            sort: '<?=$item['sort'] ?>'
        }));
        <?php 
            }
        }
        ?>    
        
        //Init Index
        indexItems();

        //Init Redactor
        $('.redactor-content').redactor({
            minHeight: '200',
            'concrete5': {
                filemanager: <?php echo $fp->canAccessFileManager()?>,
                sitemap: <?php echo $tp->canAccessSitemap()?>,
                lightbox: true
            }
        });
        
    //CREATE NEW ITEM
        
        $('.btn-add-item').click(function(){
            
            //Use the template to create a new item.
            var temp = $(".items-container .item").length;
            temp = (temp);
            itemsContainer.append(itemTemplate({
                //vars to pass to the template
                title: '',
                opt: '',
                
                //REDACTOR
                wysiwyg: '',
                
                //IMAGE SELECTOR
                fID: '',
                thumb: '',
                
                //PAGE SELECTOR
                pageID: '',
                pageName: '',
                
                sort: temp
            }));
            
            var thisModal = $(this).closest('.ui-dialog-content');
            var newItem = $('.items-container .item').last();
            thisModal.scrollTop(newItem.offset().top);
            
            //Init Redactor
            newItem.find('.redactor-content').redactor({
                minHeight: '100',
                'concrete5': {
                    filemanager: <?php echo $fp->canAccessFileManager()?>,
                    sitemap: <?php echo $tp->canAccessSitemap()?>,
                    lightbox: true
                }
            });
            
            //Init Index
            indexItems();
        });    

});
</script>