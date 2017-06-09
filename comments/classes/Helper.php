<?php namespace Jab\Comments\Classes;

use DB;
use Log;

class Helper {

    private function getOneLevel($id)
    {
        $results = Db::select('select id from jab_comments_comments where pid = ?', [$id]);

        $ids = array();
        if ($results) {
            foreach ($results as $result) {
                $ids[] = $result->id;
            }
        }
        return $ids;
    }

    public function getChildren($parent_id)
    {
        $tree = Array();
        if (!empty($parent_id)) {
            $tree = $this->getOneLevel($parent_id);
            foreach ($tree as $key => $val) {
                $ids = $this->getChildren($val);
                $tree = array_merge($tree, $ids);
            }
        }
        return $tree;
    }
}