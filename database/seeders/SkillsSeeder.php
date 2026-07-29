<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillsSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // ── Pathfinder skills (all ranks) ────────────────────────────────
            [
                'name'               => 'Electric Bike (Brain Box & Light)',
                'category'           => 'pathfinder',
                'club_rank'          => null,
                'facilitator'        => 'TBD',
                'requirement'        => 'Basic understanding of electronics',
                'curriculum'         => 'Introduction to electric circuits, wiring, battery systems, and assembly',
                'maximum_attendees'  => 30,
            ],
            [
                'name'               => 'Graphic Design & Branding',
                'category'           => 'pathfinder',
                'club_rank'          => null,
                'facilitator'        => 'TBD',
                'requirement'        => 'Smartphone or laptop recommended',
                'curriculum'         => 'Brand identity, logo design, colour theory, Canva, and typography basics',
                'maximum_attendees'  => 30,
            ],

            // ── Senior Youth — Ambassador rank ────────────────────────────────
            [
                'name'               => 'AI Automation',
                'category'           => 'senior_youth',
                'club_rank'          => 'Ambassador',
                'facilitator'        => 'TBD',
                'requirement'        => 'Laptop with internet access',
                'curriculum'         => 'Introduction to AI tools, workflow automation, prompt engineering, and practical use cases',
                'maximum_attendees'  => 25,
            ],
            [
                'name'               => 'AI Robotics',
                'category'           => 'senior_youth',
                'club_rank'          => 'Ambassador',
                'facilitator'        => 'TBD',
                'requirement'        => 'No prior experience required',
                'curriculum'         => 'Robotics fundamentals, sensor integration, programming logic, and hands-on assembly',
                'maximum_attendees'  => 25,
            ],

            // ── General skills (available to all) ────────────────────────────
            ['name' => 'STEAM',                     'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'None', 'curriculum' => 'Science, Technology, Engineering, Arts & Maths integration projects', 'maximum_attendees' => 40],
            ['name' => 'Communication & Branding',  'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'None', 'curriculum' => 'Public speaking, personal branding, storytelling, and media presence', 'maximum_attendees' => 40],
            ['name' => 'Photography & Videography', 'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Smartphone', 'curriculum' => 'Composition, lighting, mobile photography, editing, and storytelling through video', 'maximum_attendees' => 30],
            ['name' => 'Crocheting',                'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Crochet hook and yarn (provided)', 'curriculum' => 'Basic stitches, patterns, and creating simple items', 'maximum_attendees' => 35],
            ['name' => 'Tie & Dye',                 'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Old white clothing or fabric (provided)', 'curriculum' => 'Folding techniques, dye mixing, colour setting, and pattern creation', 'maximum_attendees' => 35],
            ['name' => 'CPR',                       'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'None', 'curriculum' => 'Cardiopulmonary resuscitation, choking response, and basic first aid', 'maximum_attendees' => 40],
            ['name' => 'Art',                       'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Art materials (provided)', 'curriculum' => 'Drawing techniques, colour theory, and expression through visual art', 'maximum_attendees' => 35],
            ['name' => 'Music',                     'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'None', 'curriculum' => 'Basic music theory, vocal training, and instrument introduction', 'maximum_attendees' => 35],
            ['name' => 'Bag Making',                'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Materials provided', 'curriculum' => 'Pattern cutting, sewing basics, and assembly of fabric bags', 'maximum_attendees' => 30],
            ['name' => 'Beads Craft',               'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Materials provided', 'curriculum' => 'Bead selection, stringing techniques, and creating jewellery and accessories', 'maximum_attendees' => 35],
            ['name' => 'Shoe Making',               'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Materials provided', 'curriculum' => 'Basic cobbling, sole attachment, and simple shoe construction', 'maximum_attendees' => 25],
            ['name' => 'Fashion Trends',            'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'None', 'curriculum' => 'Current trends, styling, modest fashion, and personal presentation', 'maximum_attendees' => 35],
            ['name' => 'Wig Making',                'category' => null, 'club_rank' => null, 'facilitator' => 'TBD', 'requirement' => 'Materials provided', 'curriculum' => 'Wig cap preparation, hair attachment, styling, and finishing', 'maximum_attendees' => 30],
        ];

        foreach ($skills as $data) {
            Skill::firstOrCreate(
                ['name' => $data['name'], 'category' => $data['category']],
                array_merge($data, ['status' => 'active', 'description' => null])
            );
        }

        // Seed the skill_registration_open setting (default: closed)
        \App\Models\CampSetting::firstOrCreate(
            ['key' => 'skill_registration_open'],
            ['label' => 'Skill Registration Open', 'value' => '0']
        );

        $this->command->info('Skills seeded: ' . count($skills) . ' skills created.');
    }
}
