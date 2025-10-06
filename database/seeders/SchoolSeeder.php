<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'University of California, Berkeley',
                'code' => 'UCB',
                'address' => 'Berkeley, CA 94720',
                'contact_email' => 'housing@berkeley.edu',
                'contact_phone' => '(510) 642-4108',
                'notes' => 'Large public university with extensive dormitory system',
                'is_active' => true,
            ],
            [
                'name' => 'Stanford University',
                'code' => 'STAN',
                'address' => 'Stanford, CA 94305',
                'contact_email' => 'residential@stanford.edu',
                'contact_phone' => '(650) 723-4808',
                'notes' => 'Private research university with residential colleges',
                'is_active' => true,
            ],
            [
                'name' => 'University of California, Los Angeles',
                'code' => 'UCLA',
                'address' => 'Los Angeles, CA 90095',
                'contact_email' => 'housing@ucla.edu',
                'contact_phone' => '(310) 206-7011',
                'notes' => 'Public university with on-campus housing for 12,000+ students',
                'is_active' => true,
            ],
            [
                'name' => 'University of Southern California',
                'code' => 'USC',
                'address' => 'Los Angeles, CA 90089',
                'contact_email' => 'housing@usc.edu',
                'contact_phone' => '(213) 740-2546',
                'notes' => 'Private university with comprehensive housing services',
                'is_active' => true,
            ],
            [
                'name' => 'California Institute of Technology',
                'code' => 'CALTECH',
                'address' => 'Pasadena, CA 91125',
                'contact_email' => 'housing@caltech.edu',
                'contact_phone' => '(626) 395-6324',
                'notes' => 'Small private research university with house system',
                'is_active' => true,
            ],
            [
                'name' => 'University of California, San Diego',
                'code' => 'UCSD',
                'address' => 'La Jolla, CA 92093',
                'contact_email' => 'housing@ucsd.edu',
                'contact_phone' => '(858) 534-4010',
                'notes' => 'Public university with residential college system',
                'is_active' => true,
            ],
            [
                'name' => 'University of California, Davis',
                'code' => 'UCD',
                'address' => 'Davis, CA 95616',
                'contact_email' => 'housing@ucdavis.edu',
                'contact_phone' => '(530) 752-2033',
                'notes' => 'Public university with extensive on-campus housing',
                'is_active' => true,
            ],
            [
                'name' => 'University of California, Irvine',
                'code' => 'UCI',
                'address' => 'Irvine, CA 92697',
                'contact_email' => 'housing@uci.edu',
                'contact_phone' => '(949) 824-6811',
                'notes' => 'Public university with modern residential communities',
                'is_active' => true,
            ],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
