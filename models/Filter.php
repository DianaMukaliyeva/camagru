<?php
class Filter {

    // Get all filters
    public function getFilters() {
        $result = Db::queryAll('SELECT `name`, `path` FROM filters');

        return $result;
    }
}
