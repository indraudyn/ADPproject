<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parwa;

class ParwaSeeder extends Seeder
{
    public function run()
    {
        $parwas = [
            ['name' => 'Adi Parwa', 'desc' => 'Kitab pertama yang menceritakan asal-usul nenek moyang Pandawa dan Korawa serta masa kecil mereka.'],
            ['name' => 'Sabha Parwa', 'desc' => 'Menceritakan kisah permainan dadu yang menyebabkan Pandawa kehilangan kerajaan dan harus diasingkan.'],
            ['name' => 'Vana Parwa', 'desc' => 'Menceritakan kisah kehidupan Pandawa selama 12 tahun masa pengasingan di hutan.'],
            ['name' => 'Virata Parwa', 'desc' => 'Menceritakan masa satu tahun penyamaran Pandawa di Kerajaan Wirata tanpa ketahuan.'],
            ['name' => 'Udyoga Parwa', 'desc' => 'Menceritakan persiapan perang Bratayudha dan upaya damai yang gagal antara Pandawa dan Korawa.'],
            ['name' => 'Bhishma Parwa', 'desc' => 'Awal perang besar di Kurukshetra, dimana Bhishma menjadi panglima perang Korawa.'],
            ['name' => 'Drona Parwa', 'desc' => 'Menceritakan kepemimpinan Drona sebagai panglima perang Korawa setelah jatuhnya Bhishma.'],
            ['name' => 'Karna Parwa', 'desc' => 'Menceritakan pengangkatan Karna sebagai panglima perang dan pertempurannya melawan Arjuna.'],
            ['name' => 'Shalya Parwa', 'desc' => 'Menceritakan Prabu Salya sebagai panglima perang terakhir Korawa dan akhir dari Duryodana.'],
            ['name' => 'Sauptika Parwa', 'desc' => 'Kisah serangan malam Aswatama yang membunuh banyak ksatria Pandawa saat mereka tidur.'],
            ['name' => 'Stri Parwa', 'desc' => 'Menceritakan ratapan para wanita yang kehilangan suami dan anak-anak mereka di medan perang.'],
            ['name' => 'Shanti Parwa', 'desc' => 'Menceritakan nasehat-nasehat Bhishma kepada Yudhistira mengenai tugas seorang raja.'],
            ['name' => 'Anushasana Parwa', 'desc' => 'Lanjutan ajaran Bhishma kepada Yudhistira tentang hukum dan kewajiban moral.'],
            ['name' => 'Ashvamedhika Parwa', 'desc' => 'Menceritakan pelaksanaan upacara Aswamedha oleh Yudhistira untuk menegakkan kedaulatan.'],
            ['name' => 'Ashramavasika Parwa', 'desc' => 'Kisah Dritarastra, Gandari, dan Kunti yang pergi ke hutan untuk bertapa di akhir hayat mereka.'],
            ['name' => 'Mausala Parwa', 'desc' => 'Menceritakan kehancuran bangsa Yadawa dan tenggelamnya kota Dwaraka.'],
            ['name' => 'Mahaprasthanika Parwa', 'desc' => 'Kisah perjalanan terakhir Pandawa mendaki Gunung Himalaya menuju surga.'],
            ['name' => 'Svargarohana Parwa', 'desc' => 'Kisah Yudhistira mencapai surga dan ujian terakhirnya sebelum berkumpul kembali dengan saudaranya.'],
        ];

        foreach ($parwas as $data) {
            Parwa::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['desc'],
                ]
            );
        }
    }
}
