<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Bahasa Indonesia)
    |--------------------------------------------------------------------------
    */

    'accepted'             => 'Kolom :attribute harus diterima.',
    'active_url'           => 'Kolom :attribute bukan URL yang valid.',
    'after'                => 'Kolom :attribute harus berupa tanggal setelah :date.',
    'after_or_equal'       => 'Kolom :attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha'                => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash'           => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array'                => 'Kolom :attribute harus berupa array.',
    'before'               => 'Kolom :attribute harus berupa tanggal sebelum :date.',
    'before_or_equal'      => 'Kolom :attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'numeric' => 'Kolom :attribute harus antara :min dan :max.',
        'file'    => 'Kolom :attribute harus antara :min dan :max kilobyte.',
        'string'  => 'Kolom :attribute harus antara :min dan :max karakter.',
        'array'   => 'Kolom :attribute harus memiliki antara :min dan :max item.',
    ],
    'boolean'              => 'Kolom :attribute harus bernilai true atau false.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'date'                 => 'Kolom :attribute bukan tanggal yang valid.',
    'date_equals'          => 'Kolom :attribute harus berupa tanggal yang sama dengan :date.',
    'date_format'          => 'Kolom :attribute tidak cocok dengan format :format.',
    'different'            => 'Kolom :attribute dan :other harus berbeda.',
    'digits'               => 'Kolom :attribute harus terdiri dari :digits digit.',
    'digits_between'       => 'Kolom :attribute harus antara :min dan :max digit.',
    'dimensions'           => 'Kolom :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             => 'Kolom :attribute memiliki nilai yang duplikat.',
    'email'                => 'Kolom :attribute harus berupa alamat email yang valid.',
    'ends_with'            => 'Kolom :attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'exists'               => 'Kolom :attribute yang dipilih tidak valid.',
    'file'                 => 'Kolom :attribute harus berupa file.',
    'filled'               => 'Kolom :attribute harus memiliki nilai.',
    'gt'                   => [
        'numeric' => 'Kolom :attribute harus lebih besar dari :value.',
        'file'    => 'Kolom :attribute harus lebih besar dari :value kilobyte.',
        'string'  => 'Kolom :attribute harus lebih besar dari :value karakter.',
        'array'   => 'Kolom :attribute harus memiliki lebih dari :value item.',
    ],
    'gte'                  => [
        'numeric' => 'Kolom :attribute harus lebih besar dari atau sama dengan :value.',
        'file'    => 'Kolom :attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'string'  => 'Kolom :attribute harus lebih besar dari atau sama dengan :value karakter.',
        'array'   => 'Kolom :attribute harus memiliki :value item atau lebih.',
    ],
    'image'                => 'Kolom :attribute harus berupa gambar.',
    'in'                   => 'Kolom :attribute yang dipilih tidak valid.',
    'in_array'             => 'Kolom :attribute tidak ada di :other.',
    'integer'              => 'Kolom :attribute harus berupa bilangan bulat.',
    'ip'                   => 'Kolom :attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => 'Kolom :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => 'Kolom :attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => 'Kolom :attribute harus berupa string JSON yang valid.',
    'lt'                   => [
        'numeric' => 'Kolom :attribute harus kurang dari :value.',
        'file'    => 'Kolom :attribute harus kurang dari :value kilobyte.',
        'string'  => 'Kolom :attribute harus kurang dari :value karakter.',
        'array'   => 'Kolom :attribute harus memiliki kurang dari :value item.',
    ],
    'lte'                  => [
        'numeric' => 'Kolom :attribute harus kurang dari atau sama dengan :value.',
        'file'    => 'Kolom :attribute harus kurang dari atau sama dengan :value kilobyte.',
        'string'  => 'Kolom :attribute harus kurang dari atau sama dengan :value karakter.',
        'array'   => 'Kolom :attribute tidak boleh memiliki lebih dari :value item.',
    ],
    'max'                  => [
        'numeric' => 'Kolom :attribute tidak boleh lebih dari :max.',
        'file'    => 'Kolom :attribute tidak boleh lebih dari :max kilobyte.',
        'string'  => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
        'array'   => 'Kolom :attribute tidak boleh lebih dari :max item.',
    ],
    'mimes'                => 'Kolom :attribute harus berupa file bertipe: :values.',
    'mimetypes'            => 'Kolom :attribute harus berupa file bertipe: :values.',
    'min'                  => [
        'numeric' => 'Kolom :attribute minimal :min.',
        'file'    => 'Kolom :attribute minimal :min kilobyte.',
        'string'  => 'Kolom :attribute minimal :min karakter.',
        'array'   => 'Kolom :attribute minimal memiliki :min item.',
    ],
    'multiple_of'          => 'Kolom :attribute harus merupakan kelipatan dari :value.',
    'not_in'               => 'Kolom :attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format kolom :attribute tidak valid.',
    'numeric'              => 'Kolom :attribute harus berupa angka.',
    'password'             => 'Password tidak benar.',
    'present'              => 'Kolom :attribute harus ada.',
    'regex'                => 'Format kolom :attribute tidak valid.',
    'required'             => 'Kolom :attribute wajib diisi.',
    'required_if'          => 'Kolom :attribute wajib diisi ketika :other bernilai :value.',
    'required_unless'      => 'Kolom :attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with'        => 'Kolom :attribute wajib diisi ketika :values ada.',
    'required_with_all'    => 'Kolom :attribute wajib diisi ketika :values ada.',
    'required_without'     => 'Kolom :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Kolom :attribute wajib diisi ketika tidak ada :values.',
    'same'                 => 'Kolom :attribute dan :other harus sama.',
    'size'                 => [
        'numeric' => 'Kolom :attribute harus berukuran :size.',
        'file'    => 'Kolom :attribute harus berukuran :size kilobyte.',
        'string'  => 'Kolom :attribute harus terdiri dari :size karakter.',
        'array'   => 'Kolom :attribute harus mengandung :size item.',
    ],
    'starts_with'          => 'Kolom :attribute harus diawali dengan salah satu dari berikut: :values.',
    'string'               => 'Kolom :attribute harus berupa string.',
    'timezone'             => 'Kolom :attribute harus berupa zona waktu yang valid.',
    'unique'               => 'Kolom :attribute sudah digunakan.',
    'uploaded'             => 'Kolom :attribute gagal diunggah.',
    'url'                  => 'Format kolom :attribute tidak valid.',
    'uuid'                 => 'Kolom :attribute harus berupa UUID yang valid.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'attributes' => [
        'email'    => 'email',
        'password' => 'password',
        'name'     => 'nama',
    ],

];
