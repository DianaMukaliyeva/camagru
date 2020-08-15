<?php
class Filter {

    // Get all filters
    public function getFilters() {
        $result = Db::queryAll('SELECT `id`, `name`, `path` FROM filters');

        return $result;
    }
}
