<?php
class Filter {

    // Get all filters
    public function getFilters() {
        $result = Db::queryAll('SELECT * FROM filters');
        return $result;
    }
}
