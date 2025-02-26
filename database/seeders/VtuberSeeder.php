<?php

namespace Database\Seeders;

use App\Models\Vtuber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VtuberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vtubers = [
            ['name' => 'Aizawa Ema', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Asumi Sena', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Tsumugi Kokage', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Hinano Tachibana', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Ichinose Uruha', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Yakumo Beni', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Kogara Toto', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Ren Kisaragi', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Nekota Tsuna', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Kaga Nazuna', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Kurumi Noah', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Tosaki Mimi', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Hanabusa Lisa', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Sumire Kaga', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Kaminari Qpi', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Shinomiya Runa', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Shiranami Ramune', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Komori Met', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Yumeno Akari', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Yano Kuromu', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Sendo Yuuhi', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Choya Hanabi', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => ''],
            ['name' => 'Amayui Moka', 'agency' => 'VSPO', 'channelUrl' => '', 'thumbnail' => '']

        ];
        foreach ($vtubers as $vtuber) {
            Vtuber::updateOrCreate(
                ['name' => $vtuber['name']], // Find by name
                [
                    'agency' => $vtuber['agency'],
                    'channelUrl' => $vtuber['channelUrl'],
                    'thumbnail' => $vtuber['thumbnail'],
                ]
            );
        }
    }
}