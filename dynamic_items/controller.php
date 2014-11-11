<?php
namespace Application\Block\DynamicItems;
use \Concrete\Core\Block\BlockController;
use Loader;
use File;

class Controller extends BlockController
{
    protected $btTable = 'btDynamicItems';
    protected $btInterfaceWidth = "650";
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceHeight = "465";

    public function getBlockTypeDescription()
    {
        return t("Add Dynamic Items to your Site");
    }

    public function getBlockTypeName()
    {
        return t("Dynamic Items");
    }

    public function add()
    {
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
    }

    public function edit()
    {
        $this->requireAsset('redactor'); 
        $this->requireAsset('core/file-manager'); 
        $this->requireAsset('core/sitemap');  
        
        $db = Loader::db();
        $items = $db->GetAll('SELECT * from btDynamicItem WHERE bID = ? ORDER BY sort', array($this->bID));
        $this->set('items', $items);
    }

    public function view()
    {
        $db = Loader::db();
        $items = $db->GetAll('SELECT * from btDynamicItem WHERE bID = ? ORDER BY sort', array($this->bID));
        $this->set('items', $items);
    }

    public function duplicate($newBID) {
        parent::duplicate($newBID);
        $db = Loader::db();
        $v = array($this->bID);
        $q = 'select * from btDynamicItem where bID = ?';
        $r = $db->query($q, $v);
        while ($row = $r->FetchRow()) {
            $vals = array($newBID,$args['title'][$i],$args['opt'][$i],$args['wysiwyg'][$i],$args['fID'][$i],$args['pageID'][$i],$args['sort'][$i]);  
            $db->execute('INSERT INTO btDynamicItem (bID, title, opt, wysiwyg, fID, pageID, sort) values(?,?,?,?,?,?,?)', $vals);
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->delete('btDynamicItem', array('bID' => $this->bID));
        parent::delete();
    }

    public function save($args)
    {
        $db = Loader::db();
        $db->execute('DELETE from btDynamicItem WHERE bID = ?', array($this->bID));
        $count = count($args['sort']);
        $i = 0;
        parent::save($args);
        while ($i < $count) {
            $vals = array($this->bID,$args['title'][$i],$args['opt'][$i],$args['wysiwyg'][$i],$args['fID'][$i],$args['pageID'][$i],$args['sort'][$i]);     
            $db->execute('INSERT INTO btDynamicItem (bID, title, opt, wysiwyg, fID, pageID, sort) values(?,?,?,?,?,?,?)', $vals);
            $i++;
        }
    }
    

}