<?php
// app/Imports/UsersImport.php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Assuming your Excel columns are: First Name, Last Name, Email, Role
        return new User([
            'Fname' => $row['first_name'],
            'Lname' => $row['last_name'],
            'email' => $row['email'],
            'role' => $row['role'],
        ]);
    }
}
