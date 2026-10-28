<?php

namespace App\Libraries;

class CustomValidation
{
    public function check_event_dates(string $event_end, string $fields, array $data): bool
    {
        $event_start = $data[$fields] ?? null;

        if (!$event_start || strtotime($event_start) >= strtotime($event_end)) {
            return false; // Validation fails
        }
        return true; // Validation passes
    }

    public function check_lastbook_date(string $lastbook_date, string $fields, array $data): bool
    {
        list($event_start_field, $event_end_field) = explode(',', $fields);
        $event_start = $data[$event_start_field] ?? null;
        $event_end = $data[$event_end_field] ?? null;

        if (!$event_start || !$event_end || strtotime($lastbook_date) < strtotime($event_start) || strtotime($lastbook_date) > strtotime($event_end)) {
            return false; // Validation fails
        }
        return true; // Validation passes
    }

    public function check_unique_name(string $name, string $fields, array $data): bool
    {
        $db = \Config\Database::connect();
    
        // Explode the string into an array
        $params = explode(',', $fields);
        if (count($params) < 3) {
            return false; // Prevent errors if parameters are missing
        }

        list($table, $column, $value) = $params;
    
        // Query to check if the name exists in the database, excluding the current ID
        $query = $db->table($table)
                    ->where($column, '!=', $value)
                    ->countAllResults();
    
        if ($query > 0) {
            return false;
        }
        return true;
    }

    public function min_array_length($value, string $min, array $data): bool
    {
        return is_array($value) && count(array_filter($value, 'strlen')) >= (int) $min;
    }
}
