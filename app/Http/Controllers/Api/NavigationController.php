<?php

namespace App\Http\Controllers\Api;

class NavigationController extends Controller
{
    /**
     * OBBC custom navigation/menu pages
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $training = [
            [
                'id' => 0,
                'name' => 'Træning',
            ],
            [
                'id' => 1,
                'name' => '  Holdtræning',
                'url' => 'http://www.obbc.dk/traening/holdtraening/',
            ],
            [
                'id' => 2,
                'name' => '  Bodybike',
                'url' => 'http://www.obbc.dk/traening/bodybike/',
            ],
            [
                'id' => 3,
                'name' => '  Crosstræning',
                'url' => 'http://www.obbc.dk/traening/crosstraining/',
            ],
            [
                'id' => 4,
                'name' => '  Styrketræning',
                'url' => 'http://www.obbc.dk/traening/styrketraening/',
            ],
            [
                'id' => 5,
                'name' => '  Styrkeløft',
                'url' => 'http://www.obbc.dk/traening/styrkeloeft/',
            ],
            [
                'id' => 6,
                'name' => '  Vi hjælper dig',
                'url' => 'http://www.obbc.dk/traening/suppler-din-traening/',
            ],
        ];

        $membership = [
            [
                'id' => 0,
                'name' => 'Medlemskab',
            ],
            [
                'id' => 1,
                'name' => '  Medlemskab & priser',
                'url' => 'http://www.obbc.dk/medlemskab/medlemskaber-og-priser/',
            ],
            [
                'id' => 2,
                'name' => '  Handelsbetingelser',
                'url' => 'http://www.obbc.dk/medlemskab/medlemskaber-og-priser/handelsbetingelser/',
            ],
            [
                'id' => 3,
                'name' => '  Medlemsfordele',
                'url' => 'http://www.obbc.dk/medlemskab/medlemsfordele/',
            ],
        ];

        $about = [
            [
                'id' => 0,
                'name' => 'Om OBBC',
            ],
            [
                'id' => 1,
                'name' => '  Om foreningen',
                'url' => 'http://www.obbc.dk/om-obbc/foreningen/',
            ],
            [
                'id' => 2,
                'name' => '  Åbningstider',
                'url' => 'http://www.obbc.dk/om-obbc/foreningen/aabningstider/',
            ],
            [
                'id' => 3,
                'name' => '  Lokaler',
                'url' => 'http://www.obbc.dk/om-obbc/lokaler-og-galleri/',
            ],
            [
                'id' => 3,
                'name' => '  Hvem er vi',
                'url' => 'http://www.obbc.dk/om-obbc/hvem-er-vi/',
            ],
            [
                'id' => 4,
                'name' => '  Vedtægter for OBBC',
                'url' => 'http://www.obbc.dk/om-obbc/generelt/vedtaegter-for-obbc/',
            ],
        ];

        $contact = [
            [
                'id' => 0,
                'name' => 'Kontakt',
            ],
            [
                'id' => 1,
                'name' => '  Kontakt OBBC',
                'url' => 'http://www.obbc.dk/kontakt/',
            ],
        ];

        $privacy = [
            [
                'id' => 1,
                'name' => 'Privatlivspolitik for OBBC App',
                'url' => 'http://165.227.174.67/obbc/privatlivspolitik',
            ],
        ];

        $result = ['data' => array_merge($training, $membership, $about, $contact, $privacy)];

        return $this->response($result);
    }
}
