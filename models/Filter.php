<?php
class Filter {

    // Get filters
    public function getFilters() {
        $result = Db::queryAll('SELECT * FROM filters');
        return $result;
    }
}
