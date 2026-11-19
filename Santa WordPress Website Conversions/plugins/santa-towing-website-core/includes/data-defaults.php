<?php
/**
 * Default Source Data Mirrors (1:1 parity with untouched React source files)
 *
 * Includes:
 * - Site Content defaults (`santa_towing_site_content`)
 * - 16 Ghana Regions & 232 Areas (`GHANA_REGIONS_DATA`)
 * - 32 Towing Adverts (`TOWING_ADVERTS`)
 * - Default Cars, Rentals, Parts, Gallery, FAQs, Careers, Services & Haulage fallback datasets
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Default nested `santa_towing_site_content` structure mirroring `src/data/site-content.json`.
 */
function stwc_get_default_site_content() {
    return array(
        'theme'        => array(
            'primary'   => '#015557',
            'secondary' => '#192e2e',
            'accent'    => '#d80000',
            'dark'      => '#131514',
            'white'     => '#ffffff',
            'black'     => '#000000',
        ),
        'global'       => array(
            'brand'           => array(
                'name'          => 'Santa Towing & Garage Services Ltd.',
                'shortName'     => 'Santa Towing',
                'tagline'       => '24/7 Emergency Towing, Haulage & Automotive Services in Ghana',
                'contactPerson' => 'Kwame Mensah (Lead Operations Director)',
            ),
            'logo'            => array(
                'url' => stwc_asset_image( 'santa-logo.png' ),
                'alt' => 'Santa Towing & Garage Services Ltd. Logo',
            ),
            'links'           => array(
                'phone'           => '0244753849',
                'phoneDisplay'    => '0244753849',
                'phoneFormatted'  => '024 475 3849',
                'phoneTel'        => 'tel:0244753849',
                'whatsapp'        => 'https://wa.me/233244753849',
                'whatsappNumber'  => '0244753849',
                'whatsappDisplay' => 'WhatsApp: 0244753849',
                'email'           => 'info@santatowing.com',
                'address'         => '123 Spintex Road, Accra, Ghana',
                'hours'           => '24/7 Towing Service',
                'coverage'        => 'Nationwide Coverage',
            ),
            'social'          => array(
                'facebook'  => 'https://facebook.com/santatowing',
                'twitter'   => 'https://twitter.com/santatowing',
                'instagram' => 'https://instagram.com/santatowing',
                'linkedin'  => 'https://linkedin.com/company/santatowing',
            ),
            'footer'          => array(
                'about'                 => "Ghana's premier automotive partner for 24/7 rapid emergency towing, heavy car recovery, certified garage repairs, car sales, and rental fleet solutions.",
                'quickLinksTitle'       => 'Quick Links',
                'servicesTitle'         => 'Our Services',
                'legalTitle'            => 'Legal and Policies',
                'stayConnectedTitle'    => 'Stay Connected',
                'newsletterText'        => 'Subscribe for roadside safety tips, car maintenance reminders, and exclusive service discounts.',
                'newsletterPlaceholder' => 'Enter your email',
                'newsletterButton'      => 'Subscribe Now',
                'newsletterSuccess'     => 'Thank you! You have successfully subscribed to Santa Towing updates.',
                'copyright'             => 'Santa Towing & Garage Services Ltd. All rights reserved.',
            ),
            'floatingButtons' => array(
                'towTooltip'      => 'Request Emergency Tow',
                'whatsappTooltip' => 'Chat on WhatsApp',
            ),
        ),
        'seo'          => array(
            'siteUrl'      => 'https://santatowingservices.com',
            'home'         => array(
                'title'       => 'Santa Towing | 24/7 Towing, Haulage, Repairs & Rentals in Ghana',
                'description' => 'Expert 24/7 emergency towing, nationwide haulage & heavy machinery transport, professional auto repairs, car sales, and premium car rentals in Ghana.',
            ),
            'services'     => array(
                'title'       => 'Comprehensive Automotive Services | Santa Towing',
                'description' => 'Explore our complete range of professional automotive services including 24/7 towing, mechanical repairs, body works, car sales, and rentals.',
            ),
            'towing'       => array(
                'title'       => '24/7 Emergency Towing & Car Recovery in Ghana | Santa Towing',
                'description' => 'Fast, reliable 24/7 emergency towing, flatbed recovery, and roadside assistance across Accra, Tema, Kumasi, and nationwide in Ghana.',
            ),
            'haulage'      => array(
                'title'       => 'Haulage & Heavy Equipment Transport Services in Ghana | Santa Towing',
                'description' => 'Professional heavy haulage, flatbed freight, lowbed machinery transport, and container logistics across Ghana. Safe, insured, and reliable cargo transport.',
            ),
            'rental'       => array(
                'title'       => 'Premium Car Rental Services in Ghana | Santa Towing',
                'description' => 'Rent reliable, fully insured sedans, SUVs, pickups, and luxury vehicles in Ghana. Flexible daily, weekly, and corporate monthly rates.',
            ),
            'sales'        => array(
                'title'       => 'Buy Certified New & Used Cars in Ghana | Santa Towing',
                'description' => 'Browse our inspected inventory of brand new, foreign used, and Ghana used cars for sale with flexible financing and warranty options.',
            ),
            'repairs'      => array(
                'title'       => 'Expert Auto Repairs & Computer Diagnostics | Santa Towing',
                'description' => 'Professional mechanical repairs, computer diagnostics, engine overhauls, brake servicing, and routine maintenance by certified technicians.',
            ),
            'bodyWorks'    => array(
                'title'       => 'Auto Body Works, Collision Repair & Spray Painting | Santa Towing',
                'description' => 'Restore your vehicle to showroom condition with our professional dent repair, chassis straightening, and oven-baked spray painting services.',
            ),
            'parts'        => array(
                'title'       => 'Genuine Auto Parts, Batteries & Accessories | Santa Towing',
                'description' => 'Shop original OEM and certified aftermarket car parts, heavy-duty batteries, tires, lubricants, and brake pads in Ghana.',
            ),
            'fleet'        => array(
                'title'       => 'Commercial Fleet Maintenance & Management | Santa Towing',
                'description' => 'Dedicated B2B fleet maintenance, priority 24/7 towing, and cost-effective servicing solutions for corporate vehicles in Ghana.',
            ),
            'blog'         => array(
                'title'       => 'Automotive Blog & Resources | Santa Towing',
                'description' => 'Read the latest automotive guides, towing tips, safety advice, and industry updates from the experts at Santa Towing.',
            ),
            'contact'      => array(
                'title'       => 'Contact Us | 24/7 Auto Support & Towing | Santa Towing',
                'description' => 'Get in touch with Santa Towing for inquiries, service bookings, or emergency 24/7 towing assistance across Ghana.',
            ),
            'faqs'         => array(
                'title'       => 'Frequently Asked Questions | Santa Towing',
                'description' => 'Find answers to common questions about our emergency towing, auto repair, car rental, and car sales services.',
            ),
            'gallery'      => array(
                'title'       => 'Our Work Gallery | Towing & Auto Repairs | Santa Towing',
                'description' => 'View our gallery showcasing successful car recoveries, auto body restorations, repair works, and our premium rental fleet.',
            ),
            'careers'      => array(
                'title'       => 'Careers | Join the Santa Towing Team',
                'description' => 'Explore exciting career opportunities for mechanics, tow truck operators, and administrative staff at Santa Towing.',
            ),
            'serviceAreas' => array(
                'title'       => 'Nationwide Towing Service Areas in Ghana | Santa Towing',
                'description' => 'Explore our comprehensive towing and roadside assistance coverage across all 16 regions, major cities, towns, and highways in Ghana.',
            ),
        ),
        'serviceAreas' => array(
            'badge'          => 'Nationwide Coverage',
            'title'          => 'Service Areas & Routes Directory',
            'description'    => 'Browse our 24/7 emergency towing, accident recovery, and intercity haulage corridors grouped by geographic zone. Fast response dispatch across all 16 regions of Ghana.',
            'ctaBadge'       => 'Need Fast Towing Service?',
            'ctaHeading'     => '24/7 Rapid Emergency Dispatch Nationwide',
            'ctaDescription' => 'Whether you need car breakdown recovery on the highway or long-haul car haulage between cities, our operators are on standby 24/7 across Ghana.',
            'template'       => array(
                'ctaHeading'       => 'Need Immediate Assistance?',
                'ctaSubtext'       => 'Call or WhatsApp us now to book your tow.',
                'callBtnLabel'     => 'Call Now',
                'whatsappBtnLabel' => 'WhatsApp Us',
                'prevAreaLabel'    => 'Previous Area',
                'nextAreaLabel'    => 'Next Area',
            ),
        ),
    );
}

/**
 * 16 Ghana Regions and 232 Areas matching `src/data/ghanaLocations.ts`.
 */
function stwc_get_ghana_regions_data() {
    return array(
        array(
            'region' => 'Greater Accra Region',
            'areas'  => array(
                'Accra', 'Achimota', 'Adenta', 'Airport Residential', 'Amasaman', 'Ashaiman', 'Ayawaso', 'Burma Camp', 'Circle', 'Dansoman', 'Dawhenya', 'Dodowa', 'Dzorwulu', 'East Legon', 'Kaneshie', 'Kasoa', 'Korle Klottey', 'Kpone', 'Kwashieman', 'La', 'Labadi', 'Lakeside', 'Madina', 'Nima', 'Ningo-Prampram', 'Nungua', 'Osu', 'Pokuase', 'Prampram', 'Spintex', 'Tema', 'Tesano', 'Teshie', 'Weija-Gbawe', 'Abeka', 'Ablekuma', 'Anyaa', 'Baatsona', 'Bortianor', 'Cantonments', 'Dome', 'Gbawe', 'Haatso', 'Kokrobite', 'Mallam', 'North Kaneshie', 'Ridge', 'Roman Ridge', 'Sakumono', 'Santa Maria', 'Sowutuom', 'Taifa', 'West Legon',
            ),
        ),
        array(
            'region' => 'Ashanti Region',
            'areas'  => array(
                'Kumasi', 'Abuakwa', 'Agona', 'Asante Akim', 'Atonsu', 'Bekwai', 'Ejisu', 'Fomena', 'Juaben', 'Konongo', 'Mampong', 'Nkawie', 'Obuasi', 'Offinso', 'Tepa', 'Asokwa', 'Ayigya', 'Bantama', 'Kejetia', 'KNUST', 'Kwadaso', 'Manhyia', 'Nhyeaso', 'Oforikrom', 'Suame', 'Subin', 'Tafo',
            ),
        ),
        array(
            'region' => 'Eastern Region',
            'areas'  => array(
                'Koforidua', 'Aburi', 'Akropong', 'Akwatia', 'Akyem Oda', 'Asamankese', 'Begoro', 'Donkorkrom', 'Nkawkaw', 'Nsawam', 'Odumase-Krobo', 'Somanya', 'Suhum', 'Adeiso', 'Akuapem', 'Krobo', 'Kwahu', 'Mpraeso', 'New Juaben',
            ),
        ),
        array(
            'region' => 'Western Region',
            'areas'  => array(
                'Sekondi', 'Takoradi', 'Agona Nkwanta', 'Asankragwa', 'Axim', 'Bawdie', 'Bogoso', 'Daboase', 'Elubo', 'Half Assini', 'Mpohor', 'Prestea', 'Samreboi', 'Shama', 'Tarkwa', 'Wassa Akropong', 'Effia', 'Essikado', 'Kojokrom', 'Kwesimintsim',
            ),
        ),
        array(
            'region' => 'Central Region',
            'areas'  => array(
                'Cape Coast', 'Apam', 'Assin Fosu', 'Dunkwa-on-Offin', 'Elmina', 'Mankessim', 'Winneba', 'Agona Swedru', 'Breman Asikuma', 'Budumburam', 'Gomoa', 'Komenda', 'Saltpond', 'Twifo Praso',
            ),
        ),
        array(
            'region' => 'Western North Region',
            'areas'  => array(
                'Sefwi Wiawso', 'Akontombra', 'Bibiani', 'Bodi', 'Dadieso', 'Enchi', 'Juaboso', 'Sefwi Bekwai', 'Debiso', 'Essam',
            ),
        ),
        array(
            'region' => 'Volta Region',
            'areas'  => array(
                'Ho', 'Aflao', 'Hohoe', 'Keta', 'Sogakope', 'Adidome', 'Agotime Ziope', 'Akatsi', 'Anloga', 'Dzodze', 'Juapong', 'Kpando',
            ),
        ),
        array(
            'region' => 'Oti Region',
            'areas'  => array(
                'Dambai', 'Jasikan', 'Kadjebi', 'Kete Krachi', 'Nkwanta', 'Nkwanta South', 'Krachi East', 'Krachi West', 'Nkonya',
            ),
        ),
        array(
            'region' => 'Northern Region',
            'areas'  => array(
                'Tamale', 'Gushegu', 'Karaga', 'Savelugu', 'Tolon', 'Yendi', 'Bimbilla', 'Kpandai', 'Kumbungu', 'Sagnarigu',
            ),
        ),
        array(
            'region' => 'North East Region',
            'areas'  => array(
                'Nalerigu', 'Gambaga', 'Walewale', 'Bunkpurugu', 'Chereponi', 'Yunyoo',
            ),
        ),
        array(
            'region' => 'Savannah Region',
            'areas'  => array(
                'Damongo', 'Bole', 'Buipe', 'Daboya', 'Salaga', 'Kalba', 'Sawla', 'Tuna',
            ),
        ),
        array(
            'region' => 'Upper East Region',
            'areas'  => array(
                'Bolgatanga', 'Bawku', 'Fumbisi', 'Navrongo', 'Paga', 'Sandema', 'Zebilla', 'Bongo', 'Garu', 'Tempane',
            ),
        ),
        array(
            'region' => 'Upper West Region',
            'areas'  => array(
                'Wa', 'Hamile', 'Jirapa', 'Lawra', 'Nandom', 'Tumu', 'Daffiama', 'Issa', 'Lambussie', 'Nadwoli',
            ),
        ),
        array(
            'region' => 'Bono Region',
            'areas'  => array(
                'Sunyani', 'Berekum', 'Dormaa Ahenkro', 'Wenchi', 'Drobo', 'Sampa', 'Sunyani West', 'Wamfie',
            ),
        ),
        array(
            'region' => 'Bono East Region',
            'areas'  => array(
                'Techiman', 'Atebubu', 'Kintampo', 'Kwame Danso', 'Nkoranza', 'Prang', 'Yeji', 'Kajaji', 'Tuobodom',
            ),
        ),
        array(
            'region' => 'Ahafo Region',
            'areas'  => array(
                'Goaso', 'Bechem', 'Duayaw Nkwanta', 'Hwidiem', 'Kenyasi', 'Mim', 'Kukuom',
            ),
        ),
    );
}

/**
 * 32 Localized Towing Advert Districts matching `src/data/towingAdverts.ts`.
 */
function stwc_get_towing_adverts() {
    return array(
        array(
            'id'          => 1,
            'slug'        => 'tema',
            'title'       => 'Car Towing at Tema',
            'district'    => 'Tema',
            'description' => "Need a tow truck in Tema? Santa Towing provides 24/7 towing assistance for car breakdowns, accidents, and roadside emergencies, helping motorists get back on the road quickly and safely.\n\nOur service covers Tema Motorway, Tema Communities, industrial areas, and surrounding roads. Once you contact us, our team will assess your situation and connect you with an available tow truck.\n\nWhether your car has broken down or needs to be transported, you can rely on Santa Towing for professional and dependable towing support in Tema.",
        ),
        array(
            'id'          => 2,
            'slug'        => 'ashaiman',
            'title'       => 'Car Towing at Ashaiman',
            'district'    => 'Ashaiman',
            'description' => "A car breakdown can happen at any time. If you are stranded in Ashaiman, Santa Towing provides 24/7 professional towing services to help you get the assistance you need.\n\nWe serve Ashaiman's main roads, residential communities, commercial areas, and nearby routes. When you need emergency towing or car recovery in Ashaiman, Santa Towing is ready to help you get moving again.",
        ),
        array(
            'id'          => 3,
            'slug'        => 'spintex',
            'title'       => 'Car Towing Services in Spintex',
            'district'    => 'Spintex',
            'description' => "Stranded in Spintex? Santa Towing provides reliable 24/7 towing assistance for breakdowns, accidents, and roadside emergencies. Our team can arrange a tow truck to your location and help transport your car safely.\n\nWhether you are on the main Spintex Road or within nearby communities, professional towing support is only a call away.",
        ),
        array(
            'id'          => 4,
            'slug'        => 'adenta',
            'title'       => 'Car Towing Services in Adenta',
            'district'    => 'Adenta',
            'description' => "Need towing assistance in Adenta? Santa Towing offers 24/7 car towing and recovery services for motorists facing unexpected breakdowns or accidents.\n\nContact our team with your location and car details, and we will arrange suitable towing assistance.",
        ),
        array(
            'id'          => 5,
            'slug'        => 'achimota',
            'title'       => 'Car Towing Services in Achimota',
            'district'    => 'Achimota',
            'description' => "Car trouble in Achimota can happen without warning. Santa Towing is available 24/7 to provide dependable towing support for broken-down, damaged, or accident-involved cars.",
        ),
        array(
            'id'          => 6,
            'slug'        => 'pokuase',
            'title'       => 'Car Towing Services in Pokuase',
            'district'    => 'Pokuase',
            'description' => "If your car breaks down in Pokuase, Santa Towing is ready to assist. Our 24/7 towing service helps motorists handle breakdowns, accidents, and car transportation needs.",
        ),
        array(
            'id'          => 7,
            'slug'        => 'airport-residential',
            'title'       => 'Car Towing Services in Airport Residential',
            'district'    => 'Airport Residential',
            'description' => "For dependable towing in Airport Residential, Santa Towing provides professional 24/7 car recovery and towing services for breakdowns, accidents, and cars requiring transportation.",
        ),
        array(
            'id'          => 8,
            'slug'        => 'accra-to-kumasi',
            'title'       => 'Car Towing Services from Accra to Kumasi',
            'district'    => 'Accra to Kumasi',
            'description' => "Need to move a car from Accra to Kumasi? Santa Towing provides reliable long-distance towing and car transportation for broken-down, damaged, or non-operational cars.",
        ),
        array(
            'id'          => 9,
            'slug'        => 'kumasi-to-accra',
            'title'       => 'Car Towing Services from Kumasi to Accra',
            'district'    => 'Kumasi to Accra',
            'description' => "Santa Towing offers dependable car transportation from Kumasi to Accra for broken-down, damaged, or relocation vehicles.",
        ),
        array(
            'id'          => 10,
            'slug'        => 'accra-to-cape-coast',
            'title'       => 'Car Towing Services from Accra to Cape Coast',
            'district'    => 'Accra to Cape Coast',
            'description' => "Moving a car from Accra to Cape Coast? Santa Towing provides professional long-distance towing between the two cities.",
        ),
        array(
            'id'          => 11,
            'slug'        => 'cape-coast-to-accra',
            'title'       => 'Car Towing Services from Cape Coast to Accra',
            'district'    => 'Cape Coast to Accra',
            'description' => "If your car needs to be transported from Cape Coast to Accra, Santa Towing provides reliable towing solutions.",
        ),
        array(
            'id'          => 12,
            'slug'        => 'accra-to-takoradi',
            'title'       => 'Car Towing Services from Accra to Takoradi',
            'district'    => 'Accra to Takoradi',
            'description' => "Need long-distance towing from Accra to Takoradi? Santa Towing provides dependable car transportation for breakdowns and accident recovery.",
        ),
        array(
            'id'          => 13,
            'slug'        => 'takoradi-to-accra',
            'title'       => 'Car Towing Services from Takoradi to Accra',
            'district'    => 'Takoradi to Accra',
            'description' => "Santa Towing assists motorists who need safe and reliable car transportation from Takoradi to Accra.",
        ),
        array(
            'id'          => 14,
            'slug'        => 'accra-to-koforidua',
            'title'       => 'Car Towing Services from Accra to Koforidua',
            'district'    => 'Accra to Koforidua',
            'description' => "For car transportation from Accra to Koforidua, Santa Towing offers dependable long-distance towing assistance.",
        ),
        array(
            'id'          => 15,
            'slug'        => 'koforidua-to-accra',
            'title'       => 'Car Towing Services from Koforidua to Accra',
            'district'    => 'Koforidua to Accra',
            'description' => "Need your car transported from Koforidua to Accra? Santa Towing provides professional towing support for damaged and broken-down cars.",
        ),
        array(
            'id'          => 16,
            'slug'        => 'accra-to-ho',
            'title'       => 'Car Towing Services from Accra to Ho',
            'district'    => 'Accra to Ho',
            'description' => "Santa Towing provides reliable car towing from Accra to Ho for motorists requiring long-distance transportation.",
        ),
        array(
            'id'          => 17,
            'slug'        => 'ho-to-accra',
            'title'       => 'Car Towing Services from Ho to Accra',
            'district'    => 'Ho to Accra',
            'description' => "If your car needs to travel from Ho to Accra, Santa Towing arranges professional towing assistance for breakdowns and relocation needs.",
        ),
        array(
            'id'          => 18,
            'slug'        => 'ningo-prampram',
            'title'       => 'Car Towing Services in Ningo-Prampram',
            'district'    => 'Ningo-Prampram',
            'description' => "Santa Towing provides 24/7 car towing services in Ningo-Prampram for motorists facing breakdowns, accidents, or roadside emergencies.",
        ),
        array(
            'id'          => 19,
            'slug'        => 'korle-klottey',
            'title'       => 'Car Towing Services in Korle Klottey',
            'district'    => 'Korle Klottey',
            'description' => "For towing assistance in Korle Klottey, Santa Towing is available around the clock for car breakdowns and recovery.",
        ),
        array(
            'id'          => 20,
            'slug'        => 'ayawaso',
            'title'       => 'Car Towing Services in Ayawaso',
            'district'    => 'Ayawaso',
            'description' => "Car trouble in Ayawaso? Santa Towing offers reliable 24/7 towing and car recovery services.",
        ),
        array(
            'id'          => 21,
            'slug'        => 'weija-gbawe',
            'title'       => 'Car Towing Services in Weija-Gbawe',
            'district'    => 'Weija-Gbawe',
            'description' => "If you experience a car breakdown in Weija-Gbawe, Santa Towing provides professional 24/7 towing assistance.",
        ),
        array(
            'id'          => 22,
            'slug'        => 'nsawam',
            'title'       => 'Car Towing Services in Nsawam',
            'district'    => 'Nsawam',
            'description' => "Need towing help in Nsawam? Santa Towing provides dependable car recovery and highway towing support.",
        ),
        array(
            'id'          => 23,
            'slug'        => 'suhum',
            'title'       => 'Car Towing Services in Suhum',
            'district'    => 'Suhum',
            'description' => "Santa Towing assists motorists in Suhum and along the N6 corridor with reliable car towing and recovery.",
        ),
        array(
            'id'          => 24,
            'slug'        => 'madina',
            'title'       => 'Car Towing Services in Madina',
            'district'    => 'Madina',
            'description' => "Stranded in Madina? Santa Towing provides 24/7 towing assistance for car breakdowns and roadside emergencies.",
        ),
        array(
            'id'          => 25,
            'slug'        => 'dodowa',
            'title'       => 'Car Towing Services in Dodowa',
            'district'    => 'Dodowa',
            'description' => "For motorists in Dodowa, Santa Towing offers dependable towing and car recovery services around the clock.",
        ),
        array(
            'id'          => 26,
            'slug'        => 'amasaman',
            'title'       => 'Car Towing Services in Amasaman',
            'district'    => 'Amasaman',
            'description' => "Car trouble in Amasaman? Santa Towing provides reliable 24/7 towing services for emergency recovery.",
        ),
        array(
            'id'          => 27,
            'slug'        => 'circle',
            'title'       => 'Car Towing Services at Circle',
            'district'    => 'Circle',
            'description' => "If your car breaks down around Kwame Nkrumah Circle, Santa Towing provides 24/7 urban towing and car recovery.",
        ),
        array(
            'id'          => 28,
            'slug'        => 'nima',
            'title'       => 'Car Towing Services in Nima',
            'district'    => 'Nima',
            'description' => "Santa Towing offers reliable 24/7 towing services in Nima for breakdown recovery and car transportation.",
        ),
        array(
            'id'          => 29,
            'slug'        => 'teshie',
            'title'       => 'Car Towing Services in Teshie',
            'district'    => 'Teshie',
            'description' => "Need a tow truck in Teshie? Santa Towing provides professional car towing and recovery services around the clock.",
        ),
        array(
            'id'          => 30,
            'slug'        => 'labadi',
            'title'       => 'Car Towing Services in Labadi',
            'district'    => 'Labadi',
            'description' => "For motorists in Labadi, Santa Towing offers dependable 24/7 towing assistance for breakdowns and accidents.",
        ),
        array(
            'id'          => 31,
            'slug'        => 'lakeside',
            'title'       => 'Car Towing Services in Lakeside',
            'district'    => 'Lakeside',
            'description' => "A car breakdown in Lakeside Estate does not have to leave you stranded. Santa Towing provides 24/7 towing and recovery.",
        ),
        array(
            'id'          => 32,
            'slug'        => 'burma-camp',
            'title'       => 'Car Towing Services in Burma Camp',
            'district'    => 'Burma Camp',
            'description' => "Need car towing around Burma Camp? Santa Towing provides professional 24/7 towing and recovery services.",
        ),
    );
}

/**
 * Standard 4 sub-services per Service Area matching `ServiceAreaDetails.tsx` & `ServiceAreaTowingService.tsx`.
 */
function stwc_get_standard_area_services() {
    return array(
        'emergency-towing'     => array(
            'title' => 'Emergency Towing',
            'icon'  => 'truck',
            'desc'  => 'Fast, 24/7 response for breakdowns and accidents.',
        ),
        'scheduled-towing'     => array(
            'title' => 'Scheduled Towing',
            'icon'  => 'calendar',
            'desc'  => 'Schedule a tow truck for planned car relocation.',
        ),
        'car-recovery'         => array(
            'title' => 'Car Recovery',
            'icon'  => 'shield',
            'desc'  => 'Safe off-road and ditch recovery services.',
        ),
        'long-distance-towing' => array(
            'title' => 'Long-Distance Towing',
            'icon'  => 'route',
            'desc'  => 'Inter-city and cross-regional car transport.',
        ),
    );
}

/**
 * Default Featured Cars matching `src/data/mockData.ts`.
 */
function stwc_get_default_cars() {
    return array(
        array( 'id' => 1, 'type' => 'New', 'name' => '2024 Luxury Sedan', 'price' => 'GH₵ 540,000', 'fuel' => 'Hybrid', 'transmission' => 'Automatic', 'year' => '2024', 'mileage' => '0 km', 'image' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'url' => stwc_route_url( '/sales/1/' ) ),
        array( 'id' => 2, 'type' => 'New', 'name' => '2024 Premium SUV', 'price' => 'GH₵ 780,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2024', 'mileage' => '25 km', 'image' => stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => stwc_route_url( '/sales/2/' ) ),
        array( 'id' => 3, 'type' => 'New', 'name' => '2024 Executive Pickup', 'price' => 'GH₵ 620,000', 'fuel' => 'Diesel', 'transmission' => 'Automatic', 'year' => '2024', 'mileage' => '15 km', 'image' => stwc_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'url' => stwc_route_url( '/sales/3/' ) ),
        array( 'id' => 4, 'type' => 'New', 'name' => '2024 Passenger Van', 'price' => 'GH₵ 690,000', 'fuel' => 'Diesel', 'transmission' => 'Manual', 'year' => '2024', 'mileage' => '40 km', 'image' => stwc_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'url' => stwc_route_url( '/sales/4/' ) ),
        array( 'id' => 5, 'type' => 'Foreign Used', 'name' => '2020 Midsize Sedan', 'price' => 'GH₵ 264,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2020', 'mileage' => '72,000 km', 'image' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'url' => stwc_route_url( '/sales/5/' ) ),
        array( 'id' => 6, 'type' => 'Foreign Used', 'name' => '2019 Luxury SUV', 'price' => 'GH₵ 450,000', 'fuel' => 'Diesel', 'transmission' => 'Automatic', 'year' => '2019', 'mileage' => '65,000 km', 'image' => stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => stwc_route_url( '/sales/6/' ) ),
        array( 'id' => 7, 'type' => 'Foreign Used', 'name' => '2021 Premium Hatchback', 'price' => 'GH₵ 210,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2021', 'mileage' => '48,000 km', 'image' => stwc_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'url' => stwc_route_url( '/sales/7/' ) ),
        array( 'id' => 8, 'type' => 'Foreign Used', 'name' => '2018 Heavy Duty Pickup', 'price' => 'GH₵ 340,000', 'fuel' => 'Diesel', 'transmission' => 'Manual', 'year' => '2018', 'mileage' => '92,000 km', 'image' => stwc_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'url' => stwc_route_url( '/sales/8/' ) ),
        array( 'id' => 9, 'type' => 'Ghana Used', 'name' => '2018 Compact Hatchback', 'price' => 'GH₵ 144,000', 'fuel' => 'Petrol', 'transmission' => 'Manual', 'year' => '2018', 'mileage' => '136,000 km', 'image' => stwc_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'url' => stwc_route_url( '/sales/9/' ) ),
        array( 'id' => 10, 'type' => 'Ghana Used', 'name' => '2015 Family SUV', 'price' => 'GH₵ 185,000', 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'year' => '2015', 'mileage' => '185,000 km', 'image' => stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => stwc_route_url( '/sales/10/' ) ),
        array( 'id' => 11, 'type' => 'Ghana Used', 'name' => '2017 Commuter Sedan', 'price' => 'GH₵ 120,000', 'fuel' => 'Petrol', 'transmission' => 'Manual', 'year' => '2017', 'mileage' => '162,000 km', 'image' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'url' => stwc_route_url( '/sales/11/' ) ),
        array( 'id' => 12, 'type' => 'Ghana Used', 'name' => '2014 Utility Pickup', 'price' => 'GH₵ 165,000', 'fuel' => 'Diesel', 'transmission' => 'Manual', 'year' => '2014', 'mileage' => '210,000 km', 'image' => stwc_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'url' => stwc_route_url( '/sales/12/' ) ),
    );
}

/**
 * Default Rental Fleet matching `src/data/mockData.ts`.
 */
function stwc_get_default_rentals() {
    return array(
        array( 'id' => 1, 'name' => 'Economy', 'category' => 'Economy', 'daily' => 'GH₵ 400', 'weekly' => 'GH₵ 2,500', 'monthly' => 'GH₵ 9,000', 'seats' => '4', 'trans' => 'Auto/Manual', 'fuel' => 'Petrol', 'image' => stwc_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'url' => stwc_route_url( '/rental/1/' ) ),
        array( 'id' => 2, 'name' => 'Sedan', 'category' => 'Sedan', 'daily' => 'GH₵ 600', 'weekly' => 'GH₵ 3,800', 'monthly' => 'GH₵ 14,000', 'seats' => '5', 'trans' => 'Automatic', 'fuel' => 'Petrol/Hybrid', 'image' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'url' => stwc_route_url( '/rental/2/' ) ),
        array( 'id' => 3, 'name' => 'SUV', 'category' => 'SUV', 'daily' => 'GH₵ 900', 'weekly' => 'GH₵ 5,800', 'monthly' => 'GH₵ 21,000', 'seats' => '7', 'trans' => 'Automatic', 'fuel' => 'Petrol/Diesel', 'image' => stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'url' => stwc_route_url( '/rental/3/' ) ),
        array( 'id' => 4, 'name' => 'Pickup', 'category' => 'Pickup', 'daily' => 'GH₵ 800', 'weekly' => 'GH₵ 5,000', 'monthly' => 'GH₵ 18,000', 'seats' => '5', 'trans' => 'Automatic', 'fuel' => 'Diesel', 'image' => stwc_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'url' => stwc_route_url( '/rental/4/' ) ),
        array( 'id' => 5, 'name' => 'Luxury', 'category' => 'Luxury', 'daily' => 'GH₵ 1,500', 'weekly' => 'GH₵ 9,500', 'monthly' => 'GH₵ 35,000', 'seats' => '5', 'trans' => 'Automatic', 'fuel' => 'Premium', 'image' => stwc_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'url' => stwc_route_url( '/rental/5/' ) ),
        array( 'id' => 6, 'name' => 'Van', 'category' => 'Van', 'daily' => 'GH₵ 1,100', 'weekly' => 'GH₵ 7,000', 'monthly' => 'GH₵ 25,000', 'seats' => '12', 'trans' => 'Auto/Manual', 'fuel' => 'Diesel', 'image' => stwc_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'url' => stwc_route_url( '/rental/6/' ) ),
    );
}

/**
 * Default Auto Parts matching `src/pages/PartDetails.tsx`.
 */
function stwc_get_default_parts() {
    return array(
        array( 'id' => 1, 'name' => 'Premium Heavy Duty Battery 70Ah', 'brand' => 'Bosch', 'category' => 'Car Batteries', 'price' => 'GH₵ 850', 'sku' => 'BSH-70AH-HD', 'rating' => 5.0, 'image' => stwc_asset_image( 'image-9-car-battery-maintenance.jpg' ), 'url' => stwc_route_url( '/parts/1/' ) ),
        array( 'id' => 2, 'name' => 'Synthetic Motor Oil 5W-30 (5L)', 'brand' => 'Castrol', 'category' => 'Engine Oil', 'price' => 'GH₵ 420', 'sku' => 'CST-5W30-5L', 'rating' => 4.8, 'image' => stwc_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'url' => stwc_route_url( '/parts/2/' ) ),
        array( 'id' => 3, 'name' => 'Ceramic Brake Pads (Front)', 'brand' => 'Brembo', 'category' => 'Brake Pads', 'price' => 'GH₵ 650', 'sku' => 'BRM-CER-FR', 'rating' => 4.9, 'image' => stwc_asset_image( 'image-11-brake-parts-disk-maintenance.jpg' ), 'url' => stwc_route_url( '/parts/3/' ) ),
        array( 'id' => 4, 'name' => 'All-Season Tire 205/55R16', 'brand' => 'Michelin', 'category' => 'Tires', 'price' => 'GH₵ 1,200', 'sku' => 'MCH-205-55-16', 'rating' => 5.0, 'image' => stwc_asset_image( 'image-12-tyre-maintenance.jpg' ), 'url' => stwc_route_url( '/parts/4/' ) ),
        array( 'id' => 5, 'name' => 'Engine Air Filter', 'brand' => 'K&N', 'category' => 'Filters', 'price' => 'GH₵ 350', 'sku' => 'KN-AIR-FLTR', 'rating' => 4.7, 'image' => stwc_asset_image( 'image-13-filters-maintenance.jpg' ), 'url' => stwc_route_url( '/parts/5/' ) ),
        array( 'id' => 6, 'name' => 'LED Headlight Bulbs H7 (Pair)', 'brand' => 'Philips', 'category' => 'Lighting', 'price' => 'GH₵ 550', 'sku' => 'PHL-LED-H7', 'rating' => 4.6, 'image' => stwc_asset_image( 'image-15-car-lighting-maintenance.jpg' ), 'url' => stwc_route_url( '/parts/6/' ) ),
    );
}

/**
 * Default FAQs matching `src/pages/Faqs.tsx`.
 */
function stwc_get_default_faqs() {
    return array(
        array( 'category' => 'Towing', 'q' => 'What is your average response time for emergency towing?', 'a' => 'Within the Greater Accra and Ashanti regions, we guarantee a response time of 30-45 minutes. For areas outside major city limits, response times may vary depending on distance and traffic conditions.' ),
        array( 'category' => 'Towing', 'q' => 'What areas do your towing services cover?', 'a' => 'We offer nationwide coverage through our owned fleet and vetted partner network, with rapid-response zones in Accra, Tema, and Kumasi.' ),
        array( 'category' => 'Towing', 'q' => 'How much does a standard tow cost?', 'a' => 'Towing costs depend on the distance and the type of car. Our base rate starts at GH₵ 250 within city limits. We provide upfront pricing before dispatching a truck.' ),
        array( 'category' => 'Rentals', 'q' => 'What are the eligibility requirements to rent a car?', 'a' => 'You must be at least 21 years old (25 for luxury or SUV classes), possess a valid Ghanaian driver’s license or International Driving Permit, and provide a refundable security deposit.' ),
        array( 'category' => 'Rentals', 'q' => 'Can I rent a car with a driver?', 'a' => 'Yes, we offer chauffeur services for an additional daily fee. All our drivers are professionally trained, vetted, and familiar with local routes.' ),
        array( 'category' => 'Repairs', 'q' => 'Do you offer a warranty on repairs?', 'a' => 'Yes, all our mechanical repairs and labor come with a minimum 6-month or 10,000 km warranty. Parts warranties typically range from 1 to 2 years.' ),
        array( 'category' => 'Sales', 'q' => 'Do you offer car financing?', 'a' => 'Yes, we partner with leading local banks and financial institutions to offer flexible auto loan options with competitive interest rates.' ),
        array( 'category' => 'Sales', 'q' => 'Are your used cars inspected before sale?', 'a' => 'Absolutely. Every pre-owned car undergoes a rigorous 150-point mechanical, electrical, and structural inspection before being listed for sale.' ),
        array( 'category' => 'Parts', 'q' => 'How do I know if a part is compatible with my car?', 'a' => 'Provide your car’s Chassis Number (VIN) and our parts specialists will verify 100% compatibility before purchase.' ),
        array( 'category' => 'Fleet', 'q' => 'What is the minimum number of cars for a fleet account?', 'a' => 'Our corporate fleet accounts typically start at 5 cars, with tailored solutions available for growing businesses.' ),
        array( 'category' => 'Payments', 'q' => 'What payment methods do you accept?', 'a' => 'We accept Cash, major Credit/Debit Cards, Mobile Money (MTN, Telecel, AT), and bank transfers for corporate accounts.' ),
    );
}

/**
 * Default Gallery Items matching `src/pages/Gallery.tsx`.
 */
function stwc_get_default_gallery_items() {
    return array(
        array( 'id' => 1, 'category' => 'Workshop', 'url' => stwc_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'title' => 'State-of-the-art diagnostic bay', 'span' => 'col-span-1 row-span-2', 'media_type' => 'photo', 'location' => 'Spintex Workshop' ),
        array( 'id' => 2, 'category' => 'Towing Fleet', 'url' => stwc_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ), 'title' => 'Heavy-duty recovery car', 'span' => 'col-span-2 row-span-1', 'media_type' => 'photo', 'location' => 'Tema Motorway' ),
        array( 'id' => 3, 'category' => 'Team & Facilities', 'url' => stwc_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'title' => 'Our expert mechanics', 'span' => 'col-span-1 row-span-1', 'media_type' => 'photo', 'location' => 'Accra Facility' ),
        array( 'id' => 4, 'category' => 'Car Sales', 'url' => stwc_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'title' => 'Premium pre-owned inventory', 'span' => 'col-span-1 row-span-1', 'media_type' => 'photo', 'location' => 'Showroom' ),
        array( 'id' => 5, 'category' => 'Rental Fleet', 'url' => stwc_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'title' => 'Luxury SUV rentals', 'span' => 'col-span-1 row-span-2', 'media_type' => 'photo', 'location' => 'Airport Pickup' ),
        array( 'id' => 6, 'category' => 'Body Works', 'url' => stwc_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'title' => 'Custom paint job', 'span' => 'col-span-2 row-span-2', 'media_type' => 'photo', 'location' => 'Tema Paint Booth' ),
        array( 'id' => 7, 'category' => 'Workshop', 'url' => stwc_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'title' => 'Engine overhaul in progress', 'span' => 'col-span-1 row-span-1', 'media_type' => 'photo', 'location' => 'Accra Workshop' ),
        array( 'id' => 8, 'category' => 'Towing Fleet', 'url' => stwc_asset_image( 'image-1-towing-vehicle.jpg' ), 'title' => 'City-wide flatbed towing', 'span' => 'col-span-1 row-span-1', 'media_type' => 'photo', 'location' => 'East Legon' ),
    );
}

/**
 * Default Career Openings matching `src/pages/Careers.tsx`.
 */
function stwc_get_default_careers() {
    return array(
        array(
            'id'               => 1,
            'title'            => 'Senior Master Technician',
            'department'       => 'Auto Repair',
            'location'         => 'Accra Main Workshop',
            'type'             => 'Full-time',
            'description'      => 'We are seeking an experienced Senior Master Technician to lead complex diagnostics and repairs.',
            'responsibilities' => array(
                'Perform advanced diagnostic tests on modern car systems.',
                'Lead and mentor junior technicians in best practices.',
                'Ensure all repairs meet strict quality and safety standards.',
                'Maintain accurate service records and communicate with service advisors.',
            ),
            'requirements'     => array(
                'Minimum 7+ years of professional auto repair experience.',
                'Advanced diagnostic certifications or equivalent.',
                'Strong leadership and communication skills.',
                'Ability to operate modern diagnostic equipment.',
            ),
            'benefits'         => array( 'Competitive base salary', 'Performance bonuses', 'Health insurance', 'Tool allowance', 'Paid time off' ),
        ),
        array(
            'id'               => 2,
            'title'            => 'Heavy Duty Tow Truck Operator',
            'department'       => 'Towing & Recovery',
            'location'         => 'Kumasi Branch',
            'type'             => 'Full-time',
            'description'      => 'Join our rapid response fleet as a Heavy Duty Tow Truck Operator handling commercial and complex recoveries.',
            'responsibilities' => array(
                'Operate heavy-duty towing and recovery equipment safely.',
                'Provide excellent customer service during stressful roadside situations.',
                'Perform daily car inspections and basic maintenance.',
            ),
            'requirements'     => array(
                'Valid heavy-duty commercial driver\'s license.',
                'Clean driving record and experience in towing or heavy equipment.',
                'Ability to work flexible shifts including nights and weekends.',
            ),
            'benefits'         => array( 'Competitive hourly rate', 'Overtime opportunities', 'Health insurance', 'Uniform provided', 'Safety training' ),
        ),
        array(
            'id'               => 3,
            'title'            => 'Service Advisor',
            'department'       => 'Customer Support',
            'location'         => 'Accra Main Workshop',
            'type'             => 'Full-time',
            'description'      => 'Act as the primary liaison between our customers and the workshop team, ensuring a seamless service experience.',
            'responsibilities' => array(
                'Greet customers and understand their car issues.',
                'Provide accurate cost estimates and repair timelines.',
                'Keep customers updated on repair progress.',
            ),
            'requirements'     => array(
                'Previous experience in automotive customer service or sales.',
                'Excellent verbal and written communication skills.',
                'Basic understanding of automotive mechanics.',
            ),
            'benefits'         => array( 'Base salary + commission', 'Health insurance', 'Paid training', 'Career advancement opportunities' ),
        ),
        array(
            'id'               => 4,
            'title'            => 'Auto Body Painter',
            'department'       => 'Body Works',
            'location'         => 'Tema Facility',
            'type'             => 'Full-time',
            'description'      => 'Bring cars back to their original showroom shine as a precision Auto Body Painter.',
            'responsibilities' => array(
                'Prepare surfaces, mix paints, and match colors precisely.',
                'Apply primer, paint, and clear coats using spray guns.',
                'Maintain a clean and safe spray booth environment.',
            ),
            'requirements'     => array(
                '3+ years of professional auto painting experience.',
                'Knowledge of modern waterborne and solvent paint systems.',
                'Attention to detail and safety compliance.',
            ),
            'benefits'         => array( 'Competitive salary', 'Performance bonuses', 'Health insurance', 'State-of-the-art spray booth' ),
        ),
    );
}
