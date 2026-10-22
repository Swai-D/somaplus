<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bulk import of students
    |--------------------------------------------------------------------------
    |
    | The most data rows one CSV file may hold. A larger school uploads in parts.
    |
    */

    'import_max_rows' => 1000,

    /*
    |--------------------------------------------------------------------------
    | ID cards
    |--------------------------------------------------------------------------
    |
    | The most ID cards printed in one go. A larger class is printed in parts.
    |
    */

    'id_card_max' => 200,

];
