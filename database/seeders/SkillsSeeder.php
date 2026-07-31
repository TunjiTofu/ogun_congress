<?php

namespace Database\Seeders;

use App\Models\CampSetting;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillsSeeder extends Seeder
{
    public function run(): void
    {
        // Categories:
        // PF + SY = ['pathfinder', 'senior_youth']  (most skills)
        // SY only = ['senior_youth']                 (Graphic Design, AI Automation, Comm & Branding)

        $pfsy = ['pathfinder', 'senior_youth'];
        $sy   = ['senior_youth'];

        $skills = [
            [
                'name'               => 'Electric Bike (Brain Box and Light)',
                'target_categories'  => $pfsy,
                'requirement'        => '<p><strong>Option A:</strong> ₦7,000 for Brain Box</p><p><strong>Option B:</strong> ₦3,000 for Light</p><p>You may choose either or both options.</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Graphic Design and Branding',
                'target_categories'  => $sy,
                'requirement'        => '<p>Phone</p><p>₦3,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'AI Automation',
                'target_categories'  => $sy,
                'requirement'        => '<p>Laptop (required)</p><p>₦3,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'STEAM',
                'target_categories'  => $pfsy,
                'requirement'        => '<p>Starter kit &mdash; ₦5,000</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Communication and Branding',
                'target_categories'  => $sy,
                'requirement'        => '<p>Phone</p><p>₦3,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Photography and Videography',
                'target_categories'  => $pfsy,
                'requirement'        => '<p>Phone or Camera</p><p>₦3,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Crocheting',
                'target_categories'  => $pfsy,
                'requirement'        => '<p>Crochet pin and Yarns</p><p>₦3,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Tie-n-Dye',
                'target_categories'  => $pfsy,
                'requirement'        => '<p>2 yards of White Material &mdash; ₦2,500</p><p>₦3,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Cardiopulmonary Resuscitation (CPR)',
                'target_categories'  => $pfsy,
                'requirement'        => '<p>₦2,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Art',
                'target_categories'  => $pfsy,
                'requirement'        => '<p><strong>Option A:</strong> ₦5,000 for art materials (all provided)</p>'
                    . '<p><strong>Option B:</strong> Bring your own materials:</p>'
                    . '<p><strong>Drawing Class</strong></p>'
                    . '<ul><li>Staedtler pencils</li><li>Eraser</li><li>White cardboards</li>'
                    . '<li>Cotton bud (wooden handle)</li><li>Toilet rolls</li><li>Blades</li></ul>'
                    . '<p><strong>Painting Classes</strong></p>'
                    . '<ul><li>White cardboards</li><li>Paint brushes</li><li>Water container</li>'
                    . '<li>Rags</li><li>Colours (Acrylic, Postal, Watercolour, Pencil colours, or Pastel &mdash; any one)</li></ul>'
                    . '<p><strong>Collage</strong></p>'
                    . '<ul><li>White cardboard</li><li>Magazines, coloured papers or calendars</li>'
                    . '<li>Scissors &amp; Blades</li><li>Top Bond (gum)</li>'
                    . '<li>Rags, Pencils, Eraser</li></ul>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Music',
                'target_categories'  => $pfsy,
                'requirement'        => '<p><strong>Option A:</strong> Bring a Recorder</p>'
                    . '<p><strong>Option B:</strong> ₦2,500 (recorder) + ₦1,000 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Bag Making',
                'target_categories'  => $pfsy,
                'requirement'        => '<p><strong>Option A:</strong> ₦4,900 (all materials provided)</p>'
                    . '<p><strong>Option B:</strong> Bring your own materials:</p>'
                    . '<ol><li>Sewing machine (optional)</li>'
                    . '<li>2 yards of Ankara material (from home)</li>'
                    . '<li>1 thread &mdash; ₦200</li>'
                    . '<li>1 pack of hand needles &mdash; ₦500</li>'
                    . '<li>1 small scissor &mdash; ₦1,000</li>'
                    . '<li>1 small tape rule &mdash; ₦200</li>'
                    . '<li>1 tailor chalk &mdash; ₦500</li>'
                    . '<li>1 tailor ruler &mdash; ₦1,000 (optional)</li>'
                    . '<li>2 yards of zip cloth &mdash; ₦200</li>'
                    . '<li>Half dozen of zip control &mdash; ₦300</li>'
                    . '<li>Cloth/Bag lining (optional) &mdash; ₦500</li>'
                    . '<li>1 yard of leather (optional) &mdash; ₦1,500</li></ol>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Beads Craft',
                'target_categories'  => $pfsy,
                'requirement'        => '<p><strong>Option A:</strong> ₦5,000 (all materials provided)</p>'
                    . '<p><strong>Option B:</strong> Bring your own: Beads, Fishing line, Belt hook, Big Buttons</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Shoe Making',
                'target_categories'  => $pfsy,
                'requirement'        => '<p>Materials fee: ₦7,000</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Fashion Trends',
                'target_categories'  => $pfsy,
                'requirement'        => '<p>Gele</p><p>Make-up kit</p><p>₦3,500 participation fee</p>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
            [
                'name'               => 'Wig Making',
                'target_categories'  => $pfsy,
                'requirement'        => '<p><strong>Starter Kit Option:</strong> ₦5,000 covers all basic materials:<br/>'
                    . 'Needle, Thread (pack), Dome cap (pack), Scissors, T-pin, Elastic band, Comb</p>'
                    . '<p><strong>Advanced Level — bring your own:</strong></p>'
                    . '<ol><li>Mannequin head &mdash; ₦8,500 <em>or</em> Canvas head &mdash; ₦17,000</li>'
                    . '<li>Tripod stand &mdash; ₦14,000 <em>or</em> Table clamp &mdash; ₦8,000</li>'
                    . '<li>Pack of needles &mdash; ₦1,000</li>'
                    . '<li>Thread (big size) &mdash; ₦2,500</li>'
                    . '<li>Dome cap &mdash; ₦1,000</li>'
                    . '<li>Weavon (3 types)</li>'
                    . '<li>Closure</li>'
                    . '<li>Frontal</li>'
                    . '<li>Scissors &mdash; ₦2,000</li></ol>',
                'curriculum'         => null,
                'facilitator'        => null,
                'maximum_attendees'  => 100,
            ],
        ];

        // Wipe old skill records — disable FK checks first to allow truncate
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\CamperSkillRegistration::truncate();
        Skill::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($skills as $data) {
            Skill::create(array_merge($data, [
                'category'    => null,
                'club_rank'   => null,
                'status'      => 'active',
                'description' => null,
            ]));
        }

        // Seed the skill_registration_open setting (default: closed)
        CampSetting::firstOrCreate(
            ['key'   => 'skill_registration_open'],
            ['label' => 'Skill Registration Open', 'value' => '0']
        );

        $this->command->info('Skills seeded: ' . count($skills) . ' skills (Pathfinder + Senior Youth).');
        $this->command->info('Senior Youth only: Graphic Design, AI Automation, Communication and Branding.');
    }
}
