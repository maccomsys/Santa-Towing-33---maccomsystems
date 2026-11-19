<?php
/**
 * Default Content, Document Settings, Site Images, Avatar Presets & Standalone CPT Inventory Manager.
 *
 * Fully standalone: operates without Elementor and without requiring `santa-towing-website-core`,
 * while sharing the exact `santa_towing_site_content` WP option and `st_*` CPTs & `_st_*` post meta keys
 * when co-installed with the Santa Towing theme or core plugin.
 *
 * @package Santa_Towing_Business_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STBM_Defaults {

	const OPTION_SITE_CONTENT = 'santa_towing_site_content';
	const OPTION_DOC_SETTINGS = 'stbm_doc_settings_v1';

	/**
	 * Default Document Settings matching React DEFAULT_SETTINGS.
	 *
	 * @return array
	 */
	public static function get_default_doc_settings() {
		return array(
			'companyName'               => 'Santa Towing & Garage Services Ltd.',
			'tagline'                   => '24/7 Rapid Emergency Towing, Inter-City Haulage & Auto Garage',
			'address'                   => 'Spintex Road, Near Coca-Cola Roundabout, P.O. Box SK 450, Accra, Ghana',
			'phone'                     => '0244753849',
			'phoneSecondary'            => '0501234567',
			'email'                     => 'dispatch@santatowing.com',
			'website'                   => 'www.santatowing.com',
			'taxRegistrationNumber'     => 'TIN: P0028475910',
			'logoUrl'                   => stbm_asset_image( 'santa-logo.png' ),
			'primaryColor'              => '#015557',
			'currency'                  => 'GH₵',
			'defaultValidityDays'       => 14,
			'defaultTaxRate'            => 15,
			'defaultPaymentInstructions' => "1. MTN Mobile Money (Merchant ID): 0244753849 (Santa Towing & Garage Services)\n2. Ecobank Ghana: Account No: 1441002384920, Spintex Branch\n3. Cash payments accepted with verified stamped receipt.",
			'defaultTerms'              => "1. This Pro Forma Invoice is valid for 14 calendar days from the date of issue.\n2. Roadside dispatch commences upon confirmed payment or purchase order acceptance.\n3. Fuel surcharges or statutory toll fees outside Greater Accra are included where itemized.\n4. Santa Towing warrants vehicle handling with comprehensive recovery insurance.",
			'defaultNotes'              => 'Thank you for trusting Santa Towing & Garage Services. For urgent dispatch updates, contact our 24/7 hotline at 0244753849.',
			'footerText'                => 'Santa Towing & Garage Services Ltd. • Licensed Automobile Towing & Recovery Fleet • Accra, Ghana',
		);
	}

	/**
	 * Get saved Document Settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_doc_settings() {
		$saved    = get_option( self::OPTION_DOC_SETTINGS, array() );
		$defaults = self::get_default_doc_settings();
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$merged = array_merge( $defaults, $saved );
		if ( empty( $merged['logoUrl'] ) ) {
			$merged['logoUrl'] = $defaults['logoUrl'];
		} else {
			$merged['logoUrl'] = stbm_resolve_media_url( $merged['logoUrl'] );
		}
		return $merged;
	}

	/**
	 * Save Document Settings.
	 *
	 * @param array $data Input settings.
	 * @return array
	 */
	public static function save_doc_settings( $data ) {
		$defaults = self::get_default_doc_settings();
		$clean    = array(
			'companyName'               => sanitize_text_field( $data['companyName'] ?? $defaults['companyName'] ),
			'tagline'                   => sanitize_text_field( $data['tagline'] ?? $defaults['tagline'] ),
			'address'                   => sanitize_text_field( $data['address'] ?? $defaults['address'] ),
			'phone'                     => sanitize_text_field( $data['phone'] ?? $defaults['phone'] ),
			'phoneSecondary'            => sanitize_text_field( $data['phoneSecondary'] ?? $defaults['phoneSecondary'] ),
			'email'                     => sanitize_email( $data['email'] ?? $defaults['email'] ),
			'website'                   => sanitize_text_field( $data['website'] ?? $defaults['website'] ),
			'taxRegistrationNumber'     => sanitize_text_field( $data['taxRegistrationNumber'] ?? $defaults['taxRegistrationNumber'] ),
			'logoUrl'                   => esc_url_raw( $data['logoUrl'] ?? $defaults['logoUrl'] ),
			'primaryColor'              => sanitize_hex_color( $data['primaryColor'] ?? $defaults['primaryColor'] ) ?: '#015557',
			'currency'                  => sanitize_text_field( $data['currency'] ?? $defaults['currency'] ),
			'defaultValidityDays'       => max( 1, min( 365, (int) ( $data['defaultValidityDays'] ?? 14 ) ) ),
			'defaultTaxRate'            => max( 0, min( 100, (float) ( $data['defaultTaxRate'] ?? 15 ) ) ),
			'defaultPaymentInstructions' => sanitize_textarea_field( $data['defaultPaymentInstructions'] ?? $defaults['defaultPaymentInstructions'] ),
			'defaultTerms'              => sanitize_textarea_field( $data['defaultTerms'] ?? $defaults['defaultTerms'] ),
			'defaultNotes'              => sanitize_textarea_field( $data['defaultNotes'] ?? $defaults['defaultNotes'] ),
			'footerText'                => sanitize_text_field( $data['footerText'] ?? $defaults['footerText'] ),
		);
		update_option( self::OPTION_DOC_SETTINGS, $clean );
		return $clean;
	}

	/**
	 * Return 5 Admin Avatar Presets matching AdminProfile.tsx.
	 *
	 * @return array[]
	 */
	public static function get_avatar_presets() {
		return array(
			array(
				'label' => 'Operations Lead',
				'url'   => stbm_asset_image( 'avatar-operations-lead-256.jpg' ),
			),
			array(
				'label' => 'Dispatch Director',
				'url'   => stbm_asset_image( 'avatar-dispatch-director-256.jpg' ),
			),
			array(
				'label' => 'Safety Specialist',
				'url'   => stbm_asset_image( 'avatar-safety-specialist-256.jpg' ),
			),
			array(
				'label' => 'Fleet Manager',
				'url'   => stbm_asset_image( 'avatar-fleet-manager-256.jpg' ),
			),
			array(
				'label' => 'Support Officer',
				'url'   => stbm_asset_image( 'avatar-support-officer-256.jpg' ),
			),
		);
	}

	/**
	 * Default Navigation Link Lists matching AdminLinks.tsx.
	 *
	 * @return array
	 */
	public static function get_default_nav_lists() {
		return array(
			'menuLinks'    => array(
				array( 'id' => 'm-1', 'label' => 'Home', 'path' => '/', 'visible' => true ),
				array( 'id' => 'm-2', 'label' => 'Emergency Towing', 'path' => '/towing', 'visible' => true ),
				array( 'id' => 'm-3', 'label' => 'Service Areas Directory', 'path' => '/service-areas', 'visible' => true ),
				array( 'id' => 'm-4', 'label' => 'Haulage Services', 'path' => '/haulage', 'visible' => true ),
				array( 'id' => 'm-5', 'label' => 'Car Sales', 'path' => '/sales', 'visible' => true ),
				array( 'id' => 'm-6', 'label' => 'Car Rentals', 'path' => '/rental', 'visible' => true ),
				array( 'id' => 'm-7', 'label' => 'Auto Repairs & Diagnostics', 'path' => '/repairs', 'visible' => true ),
				array( 'id' => 'm-8', 'label' => 'Auto Body Works & Spraying', 'path' => '/body-works', 'visible' => true ),
				array( 'id' => 'm-9', 'label' => 'Genuine Auto Parts', 'path' => '/parts', 'visible' => true ),
				array( 'id' => 'm-10', 'label' => 'Corporate Fleet Services', 'path' => '/fleet', 'visible' => true ),
				array( 'id' => 'm-11', 'label' => 'Contact & 24/7 Dispatch', 'path' => '/contact', 'visible' => true ),
			),
			'quickLinks'   => array(
				array( 'id' => 'ql-1', 'label' => 'Home', 'path' => '/', 'visible' => true ),
				array( 'id' => 'ql-2', 'label' => 'Haulage Services', 'path' => '/haulage', 'visible' => true ),
				array( 'id' => 'ql-3', 'label' => '24/7 Emergency Towing', 'path' => '/towing', 'visible' => true ),
				array( 'id' => 'ql-4', 'label' => 'Service Areas Directory', 'path' => '/service-areas', 'visible' => true ),
				array( 'id' => 'ql-5', 'label' => 'Auto Repair & Maintenance', 'path' => '/repairs', 'visible' => true ),
				array( 'id' => 'ql-6', 'label' => 'Car Rentals', 'path' => '/rental', 'visible' => true ),
				array( 'id' => 'ql-7', 'label' => 'Car Sales', 'path' => '/sales', 'visible' => true ),
				array( 'id' => 'ql-8', 'label' => 'Auto Parts & Batteries', 'path' => '/parts', 'visible' => true ),
				array( 'id' => 'ql-9', 'label' => 'Blog & Resources', 'path' => '/blog', 'visible' => true ),
				array( 'id' => 'ql-10', 'label' => 'Contact & Dispatch', 'path' => '/contact', 'visible' => true ),
				array( 'id' => 'ql-11', 'label' => 'Sitemap', 'path' => '/sitemap', 'visible' => true ),
			),
			'serviceLinks' => array(
				array( 'id' => 'sl-1', 'label' => 'Haulage & Heavy Transport', 'path' => '/haulage', 'visible' => true ),
				array( 'id' => 'sl-2', 'label' => 'Tema Harbour Haulage', 'path' => '/haulage', 'visible' => true ),
				array( 'id' => 'sl-3', 'label' => 'Heavy Machinery Transport', 'path' => '/haulage', 'visible' => true ),
				array( 'id' => 'sl-4', 'label' => 'Emergency Roadside Towing', 'path' => '/towing', 'visible' => true ),
				array( 'id' => 'sl-5', 'label' => 'Heavy Duty Recovery', 'path' => '/towing', 'visible' => true ),
				array( 'id' => 'sl-6', 'label' => 'Fleet Maintenance', 'path' => '/fleet', 'visible' => true ),
				array( 'id' => 'sl-7', 'label' => 'Engine & Gearbox Repairs', 'path' => '/repairs', 'visible' => true ),
				array( 'id' => 'sl-8', 'label' => 'Brake & Suspension Service', 'path' => '/repairs', 'visible' => true ),
			),
		);
	}

	/**
	 * Default Blog Posts matching src/data/blogData.ts.
	 *
	 * @return array[]
	 */
	public static function get_default_blog_posts() {
		return array(
			array(
				'id'        => 1,
				'title'     => 'What to Do If Your Car Breaks Down on a Busy Highway',
				'excerpt'   => 'Step-by-step instructions to keep yourself safe while waiting for emergency assistance or a tow truck on high-speed roads.',
				'category'  => 'Emergency Assistance',
				'image'     => stbm_asset_image( 'image-1-towing-vehicle.jpg' ),
				'author'    => 'David Mensah',
				'authorImg' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
				'date'      => 'Oct 15, 2023',
				'readTime'  => '6 min read',
				'tags'      => array( 'Safety', 'Emergency', 'Highway', 'Towing' ),
			),
			array(
				'id'        => 2,
				'title'     => '5 Essential Summer Car Care Tips',
				'excerpt'   => 'Keep your car running cool during the hot summer months with these simple engine, coolant, and tire checks.',
				'category'  => 'Car Care',
				'image'     => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ),
				'author'    => 'Sarah Osei',
				'authorImg' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
				'date'      => 'Sep 28, 2023',
				'readTime'  => '4 min read',
				'tags'      => array( 'Maintenance', 'Summer', 'Coolant', 'Tires' ),
			),
			array(
				'id'        => 3,
				'title'     => 'How to Prevent Battery Drain in Older Cars',
				'excerpt'   => 'Learn the common causes of parasitic battery drain and how to ensure your car starts reliably every morning.',
				'category'  => 'Automotive Guides',
				'image'     => stbm_asset_image( 'image-9-car-battery-maintenance.jpg' ),
				'author'    => 'Kwame A.',
				'authorImg' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ),
				'date'      => 'Sep 15, 2023',
				'readTime'  => '5 min read',
				'tags'      => array( 'Battery', 'Electrical', 'Maintenance' ),
			),
			array(
				'id'        => 4,
				'title'     => 'Understanding Flatbed vs. Wheel-Lift Towing',
				'excerpt'   => 'Which towing method is right for your car? We break down the differences and explain when to request a flatbed.',
				'category'  => 'Towing Tips',
				'image'     => stbm_asset_image( 'image-1-towing-vehicle.jpg' ),
				'author'    => 'David Mensah',
				'authorImg' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
				'date'      => 'Aug 22, 2023',
				'readTime'  => '7 min read',
				'tags'      => array( 'Towing', 'Flatbed', 'Car Recovery' ),
			),
			array(
				'id'        => 5,
				'title'     => 'Santa Towing Expands Service Fleet in Greater Accra',
				'excerpt'   => 'We are thrilled to announce the addition of three new heavy-duty recovery trucks and support cars to serve our commercial clients better.',
				'category'  => 'Company News',
				'image'     => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ),
				'author'    => 'Management',
				'authorImg' => stbm_asset_image( 'santa-logo.png' ),
				'date'      => 'Aug 10, 2023',
				'readTime'  => '3 min read',
				'tags'      => array( 'News', 'Expansion', 'Fleet' ),
			),
			array(
				'id'        => 6,
				'title'     => 'Essential Items for Your Roadside Emergency Kit',
				'excerpt'   => "Don't get caught unprepared. Here are the 10 must-have items every driver should keep in their trunk for emergencies.",
				'category'  => 'Roadside Safety',
				'image'     => stbm_asset_image( 'image-16-accessories.jpg' ),
				'author'    => 'Sarah Osei',
				'authorImg' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
				'date'      => 'Jul 05, 2023',
				'readTime'  => '5 min read',
				'tags'      => array( 'Safety', 'Emergency Kit', 'Preparation' ),
			),
		);
	}

	/**
	 * Return all 31 Site Image Definitions matching src/data/siteImagesData.ts.
	 *
	 * @return array[]
	 */
	public static function get_site_image_definitions() {
		return array(
			array( 'id' => 'global-logo', 'name' => 'Primary Company Logo', 'storageKey' => 'global.logo.url', 'altStorageKey' => 'global.logo.alt', 'page' => 'Global', 'section' => 'Header & Navigation', 'defaultUrl' => stbm_asset_image( 'santa-logo.png' ), 'defaultAlt' => 'Santa Towing & Garage Services Ltd. Official Logo', 'usedIn' => 'Used in: Top navigation bar, mobile menu drawer, and public page header', 'aspectRatio' => 'auto', 'recommendedSize' => '320x90px transparent PNG' ),
			array( 'id' => 'global-favicon', 'name' => 'Browser Favicon / App Icon', 'storageKey' => 'global.images.logo', 'altStorageKey' => 'global.images.logoAlt', 'page' => 'Global', 'section' => 'Browser Tab & Bookmarks', 'defaultUrl' => stbm_asset_image( 'santa-logo.png' ), 'defaultAlt' => 'Santa Towing Favicon Icon', 'usedIn' => 'Used in: Browser address bar, tab title icon, and home-screen shortcut', 'aspectRatio' => '1:1', 'recommendedSize' => '192x192px square icon' ),
			array( 'id' => 'home-hero-1', 'name' => 'Hero Carousel Slide 1: Heavy Recovery', 'storageKey' => 'home.hero.slides.0.image', 'altStorageKey' => 'home.hero.slides.0.imageAlt', 'page' => 'Home', 'section' => 'Hero Header Banner', 'defaultUrl' => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ), 'defaultAlt' => 'Santa Towing heavy duty recovery tow truck performing emergency highway assistance', 'usedIn' => 'Used in: Homepage primary carousel - Slide 1 (Emergency Towing)', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x1080px (16:9 high resolution)' ),
			array( 'id' => 'home-hero-2', 'name' => 'Hero Carousel Slide 2: Battery & Roadside Care', 'storageKey' => 'home.hero.slides.1.image', 'altStorageKey' => 'home.hero.slides.1.imageAlt', 'page' => 'Home', 'section' => 'Hero Header Banner', 'defaultUrl' => stbm_asset_image( 'image-9-car-battery-maintenance.jpg' ), 'defaultAlt' => 'Certified automotive technician inspecting car battery and electrical connections', 'usedIn' => 'Used in: Homepage carousel - Slide 2 (Accident Recovery & Roadside Assistance)', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x1080px (16:9 high resolution)' ),
			array( 'id' => 'home-hero-3', 'name' => 'Hero Carousel Slide 3: Engine Oil & Fluid Care', 'storageKey' => 'home.hero.slides.2.image', 'altStorageKey' => 'home.hero.slides.2.imageAlt', 'page' => 'Home', 'section' => 'Hero Header Banner', 'defaultUrl' => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'defaultAlt' => 'Mechanic pouring premium engine lubricant fluid during routine servicing', 'usedIn' => 'Used in: Homepage carousel - Slide 3 (Flatbed Towing & Safe Car Transport)', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x1080px (16:9 high resolution)' ),
			array( 'id' => 'home-hero-4', 'name' => 'Hero Carousel Slide 4: Brake System Maintenance', 'storageKey' => 'home.hero.slides.3.image', 'altStorageKey' => 'home.hero.slides.3.imageAlt', 'page' => 'Home', 'section' => 'Hero Header Banner', 'defaultUrl' => stbm_asset_image( 'image-11-brake-parts-disk-maintenance.jpg' ), 'defaultAlt' => 'High-performance brake rotor and caliper replacement in certified workshop', 'usedIn' => 'Used in: Homepage carousel - Slide 4 (Premium Car Rentals)', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x1080px (16:9 high resolution)' ),
			array( 'id' => 'home-hero-5', 'name' => 'Hero Carousel Slide 5: Tyre & Wheel Alignment', 'storageKey' => 'home.hero.slides.4.image', 'altStorageKey' => 'home.hero.slides.4.imageAlt', 'page' => 'Home', 'section' => 'Hero Header Banner', 'defaultUrl' => stbm_asset_image( 'image-12-tyre-maintenance.jpg' ), 'defaultAlt' => 'Automotive specialist checking tread depth and tyre pressure on modern wheel', 'usedIn' => 'Used in: Homepage carousel - Slide 5 (Professional Auto Repairs)', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x1080px (16:9 high resolution)' ),
			array( 'id' => 'home-about-welcome', 'name' => 'Welcome & About Us Workshop Photo', 'storageKey' => 'global.images.welcomeTowing', 'altStorageKey' => 'images.alt.welcomeTowing', 'page' => 'Home', 'section' => 'About Santa Towing Section', 'defaultUrl' => stbm_asset_image( 'welcome-towing-image.jpg' ), 'defaultAlt' => 'Santa Towing state-of-the-art repair workshop and dispatch facility in Accra', 'usedIn' => 'Used in: Homepage About Us section alongside 24/7 emergency badge', 'aspectRatio' => '4:3', 'recommendedSize' => '800x600px (4:3 ratio)' ),
			array( 'id' => 'home-mobile-app', 'name' => 'Mobile Roadside Dispatch App Screen', 'storageKey' => 'global.images.mobileScreen', 'altStorageKey' => 'images.alt.mobileScreen', 'page' => 'Home', 'section' => 'Mobile App Promo Section', 'defaultUrl' => stbm_asset_image( 'santa-app-mobile-screen.png' ), 'defaultAlt' => 'Santa Towing mobile app screenshot showing live GPS driver tracking', 'usedIn' => 'Used in: Homepage instant dispatch mobile app promo banner', 'aspectRatio' => 'auto', 'recommendedSize' => '400x700px vertical mobile render' ),
			array( 'id' => 'towing-primary-truck', 'name' => 'Flatbed Tow Truck in Action', 'storageKey' => 'global.images.towingVehicle', 'altStorageKey' => 'images.alt.towingVehicle', 'page' => 'Towing', 'section' => 'Towing Services Showcase', 'defaultUrl' => stbm_asset_image( 'image-1-towing-vehicle.jpg' ), 'defaultAlt' => 'Modern hydraulic flatbed recovery vehicle with emergency lights on highway', 'usedIn' => 'Used in: Towing service overview, Emergency Towing page, and service directory cards', 'aspectRatio' => '16:9', 'recommendedSize' => '1200x800px' ),
			array( 'id' => 'towing-heavy-duty', 'name' => 'Heavy Duty Articulated Hauler & Crane', 'storageKey' => 'global.images.heavyRecovery', 'altStorageKey' => 'images.alt.heavyRecovery', 'page' => 'Towing', 'section' => 'Commercial Truck & Bus Recovery', 'defaultUrl' => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ), 'defaultAlt' => 'Commercial heavy duty winching crane recovering stranded bus', 'usedIn' => 'Used in: Heavy duty recovery cards, Gallery, and industrial emergency towing', 'aspectRatio' => '16:9', 'recommendedSize' => '1200x800px' ),
			array( 'id' => 'haulage-heavy-construction', 'name' => 'Heavy Construction & Machinery Haulage', 'storageKey' => 'global.images.constructionHaulage', 'altStorageKey' => 'images.alt.constructionHaulage', 'page' => 'Haulage', 'section' => 'Industrial Haulage Banner', 'defaultUrl' => stbm_asset_image( 'construction_haulage_1790967998627.jpg' ), 'defaultAlt' => 'Heavy lowbed transporter carrying industrial yellow machinery equipment across Ghana', 'usedIn' => 'Used in: Haulage service page, heavy equipment logistics showcase, and homepage teaser', 'aspectRatio' => '16:9', 'recommendedSize' => '1600x900px' ),
			array( 'id' => 'rental-suv-kia', 'name' => 'Rental Fleet: 2024 Kia Sportage SUV', 'storageKey' => 'global.images.suvKiaSportage', 'altStorageKey' => 'images.alt.suvKiaSportage', 'page' => 'Rental', 'section' => 'SUV & All-Wheel Drive Fleet', 'defaultUrl' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'defaultAlt' => 'Brand new 2024 Kia Sportage SUV available for daily and executive corporate hire', 'usedIn' => 'Used in: Car Rental catalog, Contact page hero banner, and SUV rental card', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'rental-sedan-corolla', 'name' => 'Rental Fleet: Toyota Corolla Sedan', 'storageKey' => 'global.images.sedanCorolla', 'altStorageKey' => 'images.alt.sedanCorolla', 'page' => 'Rental', 'section' => 'Executive Sedan Fleet', 'defaultUrl' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'defaultAlt' => 'Clean automatic Toyota Corolla sedan for business trips and personal car rental', 'usedIn' => 'Used in: Car Rental sedan category and Car Sales inventory list', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'rental-van-hyundai', 'name' => 'Rental Fleet: Hyundai H1 Passenger Van', 'storageKey' => 'global.images.vanHyundai', 'altStorageKey' => 'images.alt.vanHyundai', 'page' => 'Rental', 'section' => 'Group & Passenger Vans', 'defaultUrl' => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'defaultAlt' => '12-passenger Hyundai H1 commercial van for corporate transport and family travel', 'usedIn' => 'Used in: Car Rental commercial vans and Corporate Fleet services', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'rental-economy-almera', 'name' => 'Rental Fleet: Nissan Almera Economy', 'storageKey' => 'global.images.economyNissan', 'altStorageKey' => 'images.alt.economyNissan', 'page' => 'Rental', 'section' => 'Budget & Economy Fleet', 'defaultUrl' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'defaultAlt' => 'Fuel-efficient Nissan Almera economy rental car with cold air-conditioning', 'usedIn' => 'Used in: Car Rental budget category', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'rental-luxury-mercedes', 'name' => 'Rental Fleet: Mercedes-Benz Luxury Sedan', 'storageKey' => 'global.images.luxuryMercedes', 'altStorageKey' => 'images.alt.luxuryMercedes', 'page' => 'Rental', 'section' => 'VIP & Luxury Fleet', 'defaultUrl' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'defaultAlt' => 'Mercedes-Benz luxury vehicle with chauffeur for weddings, diplomats, and VIP events', 'usedIn' => 'Used in: Car Rental VIP section, Body Works spray painting showcase', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'rental-pickup-hilux', 'name' => 'Rental Fleet: Toyota Hilux 4x4 Pickup', 'storageKey' => 'global.images.pickupHilux', 'altStorageKey' => 'images.alt.pickupHilux', 'page' => 'Rental', 'section' => 'Rugged 4x4 & Mining Pickups', 'defaultUrl' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'defaultAlt' => 'Rugged 2023 Toyota Hilux double cabin 4WD pickup truck for rough terrain travel', 'usedIn' => 'Used in: Car Rental pickups, Corporate Fleet Management hero banner', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'repairs-battery-care', 'name' => 'Repairs: Battery Diagnostics & Replacement', 'storageKey' => 'global.images.batteryMaintenance', 'altStorageKey' => 'images.alt.batteryMaintenance', 'page' => 'Repairs', 'section' => 'Electrical Diagnostics', 'defaultUrl' => stbm_asset_image( 'image-9-car-battery-maintenance.jpg' ), 'defaultAlt' => 'Heavy duty car battery load testing and replacement in workshop', 'usedIn' => 'Used in: Auto Repairs electrical section, Parts battery section, and Blog post 3', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'repairs-engine-fluids', 'name' => 'Repairs: Engine Oil & Fluid Flushes', 'storageKey' => 'global.images.engineFluids', 'altStorageKey' => 'images.alt.engineFluids', 'page' => 'Repairs', 'section' => 'Preventive Engine Servicing', 'defaultUrl' => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'defaultAlt' => 'Automotive technician changing engine oil and inspecting transmission fluid', 'usedIn' => 'Used in: Auto Repairs preventive care, Gallery before-and-after maintenance, Blog post 2', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'repairs-brake-servicing', 'name' => 'Repairs: Brake Discs & Caliper Overhaul', 'storageKey' => 'global.images.brakeParts', 'altStorageKey' => 'images.alt.brakeParts', 'page' => 'Repairs', 'section' => 'Braking System Safety', 'defaultUrl' => stbm_asset_image( 'image-11-brake-parts-disk-maintenance.jpg' ), 'defaultAlt' => 'New ventilated brake discs and hydraulic brake pads mounted on wheel hub', 'usedIn' => 'Used in: Auto Repairs braking services and Auto Parts brake discs inventory', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'repairs-tyres-wheels', 'name' => 'Repairs: Tyres & Computerized Alignment', 'storageKey' => 'global.images.tyreMaintenance', 'altStorageKey' => 'images.alt.tyreMaintenance', 'page' => 'Repairs', 'section' => 'Wheel & Tyre Care', 'defaultUrl' => stbm_asset_image( 'image-12-tyre-maintenance.jpg' ), 'defaultAlt' => 'Automated computerized tyre balancing and wheel alignment machine', 'usedIn' => 'Used in: Auto Repairs tyre section and wheel maintenance guides', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'parts-filters', 'name' => 'Parts: Oil, Air & Cabin Filters', 'storageKey' => 'global.images.filtersMaintenance', 'altStorageKey' => 'images.alt.filtersMaintenance', 'page' => 'Parts', 'section' => 'Replacement Filters', 'defaultUrl' => stbm_asset_image( 'image-13-filters-maintenance.jpg' ), 'defaultAlt' => 'Genuine automotive oil, fuel, and air filter replacements', 'usedIn' => 'Used in: Genuine Auto Parts catalog, Careers page hero banner', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'parts-suspension', 'name' => 'Parts: Shock Absorbers & Suspension', 'storageKey' => 'global.images.suspensionParts', 'altStorageKey' => 'images.alt.suspensionParts', 'page' => 'Parts', 'section' => 'Chassis & Suspension Components', 'defaultUrl' => stbm_asset_image( 'image-14-suspension-parts-maintenance.jpg' ), 'defaultAlt' => 'Heavy duty coil springs and gas-pressurized shock absorbers', 'usedIn' => 'Used in: Auto Parts suspension inventory and Body Works chassis alignment', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'parts-lighting', 'name' => 'Parts: Headlights, Bulbs & Electrical', 'storageKey' => 'global.images.carLighting', 'altStorageKey' => 'images.alt.carLighting', 'page' => 'Parts', 'section' => 'Automotive Lighting & Lamps', 'defaultUrl' => stbm_asset_image( 'image-15-car-lighting-maintenance.jpg' ), 'defaultAlt' => 'LED headlight assembly and electrical bulbs replacement stock', 'usedIn' => 'Used in: Auto Parts lighting category, Blog page hero banner', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'parts-accessories', 'name' => 'Parts: Accessories & Emergency Road Kits', 'storageKey' => 'global.images.autoAccessories', 'altStorageKey' => 'images.alt.autoAccessories', 'page' => 'Parts', 'section' => 'Vehicle Accessories', 'defaultUrl' => stbm_asset_image( 'image-16-accessories.jpg' ), 'defaultAlt' => 'Safety warning triangles, jump leads, tire inflators and car emergency accessories', 'usedIn' => 'Used in: Auto Parts accessories section and roadside safety kits', 'aspectRatio' => '16:9', 'recommendedSize' => '1000x600px' ),
			array( 'id' => 'blog-hero-banner', 'name' => 'Blog: Main Header Background', 'storageKey' => 'blog.heroImage', 'altStorageKey' => 'blog.heroImageAlt', 'page' => 'Blog', 'section' => 'Blog Header Banner', 'defaultUrl' => stbm_asset_image( 'image-15-car-lighting-maintenance.jpg' ), 'defaultAlt' => 'Automotive maintenance guides and road safety insights header banner', 'usedIn' => 'Used in: Blog page hero background and article header', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x800px' ),
			array( 'id' => 'contact-hero-banner', 'name' => 'Contact: Main Header Banner', 'storageKey' => 'contact.heroImage', 'altStorageKey' => 'contact.heroImageAlt', 'page' => 'Contact', 'section' => 'Contact Page Header', 'defaultUrl' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'defaultAlt' => 'Santa Towing customer support and 24/7 dispatch center', 'usedIn' => 'Used in: Contact Us page hero banner', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x800px' ),
			array( 'id' => 'careers-hero-banner', 'name' => 'Careers: Recruitment Banner', 'storageKey' => 'careers.heroImage', 'altStorageKey' => 'careers.heroImageAlt', 'page' => 'Careers', 'section' => 'Careers Page Header', 'defaultUrl' => stbm_asset_image( 'image-13-filters-maintenance.jpg' ), 'defaultAlt' => 'Join Santa Towing automotive and roadside recovery professional team', 'usedIn' => 'Used in: Careers & Job openings page banner', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x800px' ),
			array( 'id' => 'gallery-hero-banner', 'name' => 'Gallery: Work Showcase Hero', 'storageKey' => 'gallery.heroImage', 'altStorageKey' => 'gallery.heroImageAlt', 'page' => 'Gallery', 'section' => 'Gallery Hero Banner', 'defaultUrl' => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ), 'defaultAlt' => 'Photo gallery of completed roadside recoveries and automotive repair works', 'usedIn' => 'Used in: Work Gallery page hero showcase', 'aspectRatio' => '16:9', 'recommendedSize' => '1920x800px' ),
		);
	}

	/**
	 * Complete default `santa_towing_site_content` nested dictionary mirroring `src/data/site-content.json`.
	 *
	 * @return array
	 */
	public static function get_default_site_content() {
		$nav_lists = self::get_default_nav_lists();
		return array(
			'theme' => array(
				'primary'    => '#015557',
				'secondary'  => '#192e2e',
				'accent'     => '#d80000',
				'dark'       => '#131514',
				'background' => '#f8fafc',
				'white'      => '#ffffff',
				'black'      => '#000000',
			),
			'global' => array(
				'brand' => array(
					'name'          => 'Santa Towing & Garage Services Ltd.',
					'shortName'     => 'Santa Towing',
					'tagline'       => '24/7 Emergency Towing, Haulage & Automotive Services in Ghana',
					'contactPerson' => 'Kwame Mensah (Operations Lead)',
				),
				'logo' => array(
					'url' => stbm_asset_image( 'santa-logo.png' ),
					'alt' => 'Santa Towing & Garage Services Ltd. Logo',
				),
				'links' => array(
					'phone'           => '0244753849',
					'phoneDisplay'    => '0244753849',
					'phoneFormatted'  => '024 475 3849',
					'phoneTel'        => 'tel:0244753849',
					'whatsapp'        => 'https://wa.me/233244753849',
					'whatsappNumber'  => '0244753849',
					'whatsappMessage' => 'Hello Santa Towing, I need 24/7 emergency roadside assistance in Ghana.',
					'whatsappDisplay' => 'WhatsApp: 0244753849',
					'email'           => 'info@santatowing.com',
					'address'         => '123 Spintex Road, Accra, Ghana',
					'hours'           => '24/7 Towing Service',
					'coverage'        => 'Nationwide Coverage',
					'menuLinks'       => $nav_lists['menuLinks'],
					'quickLinks'      => $nav_lists['quickLinks'],
					'serviceLinks'    => $nav_lists['serviceLinks'],
				),
				'social' => array(
					'facebook'  => 'https://facebook.com/santatowing',
					'twitter'   => 'https://twitter.com/santatowing',
					'instagram' => 'https://instagram.com/santatowing',
					'linkedin'  => 'https://linkedin.com/company/santatowing',
				),
				'images' => array(
					'logo'                => stbm_asset_image( 'santa-logo.png' ),
					'logoAlt'             => 'Santa Towing Favicon Icon',
					'towingVehicle'       => stbm_asset_image( 'image-1-towing-vehicle.jpg' ),
					'suvKiaSportage'      => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
					'sedanCorolla'        => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ),
					'vanHyundai'          => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ),
					'economyNissan'       => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ),
					'luxuryMercedes'      => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ),
					'pickupHilux'         => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
					'heavyRecovery'       => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ),
					'batteryMaintenance'  => stbm_asset_image( 'image-9-car-battery-maintenance.jpg' ),
					'engineFluids'        => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ),
					'brakeParts'          => stbm_asset_image( 'image-11-brake-parts-disk-maintenance.jpg' ),
					'tyreMaintenance'     => stbm_asset_image( 'image-12-tyre-maintenance.jpg' ),
					'filtersMaintenance'  => stbm_asset_image( 'image-13-filters-maintenance.jpg' ),
					'suspensionParts'     => stbm_asset_image( 'image-14-suspension-parts-maintenance.jpg' ),
					'carLighting'         => stbm_asset_image( 'image-15-car-lighting-maintenance.jpg' ),
					'autoAccessories'     => stbm_asset_image( 'image-16-accessories.jpg' ),
					'welcomeTowing'       => stbm_asset_image( 'welcome-towing-image.jpg' ),
					'mobileScreen'        => stbm_asset_image( 'santa-app-mobile-screen.png' ),
					'constructionHaulage' => stbm_asset_image( 'construction_haulage_1790967998627.jpg' ),
				),
				'nav' => array(
					'home'            => 'Home',
					'towingDropdown'  => 'Towing Services',
					'emergencyTowing' => 'Emergency Towing',
					'serviceAreas'    => 'Service Areas Directory',
					'haulage'         => 'Haulage Services',
					'carSales'        => 'Car Sales',
					'carRentals'      => 'Car Rentals',
					'garageDropdown'  => 'Garage Services',
					'repairs'         => 'Auto Repairs & Diagnostics',
					'bodyWorks'       => 'Auto Body Works & Spraying',
					'parts'           => 'Genuine Auto Parts',
					'fleet'           => 'Corporate Fleet Services',
					'allServices'     => 'All Services Overview',
					'companyDropdown' => 'Company',
					'gallery'         => 'Gallery',
					'blog'            => 'Blog & Advice',
					'faqs'            => 'FAQs',
					'sitemap'         => 'Website Sitemap',
					'contact'         => 'Contact',
					'search'          => 'Search',
					'callNow'         => 'Call Now: 0244753849',
				),
				'footer' => array(
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
			'seo' => array(
				'siteUrl'      => 'https://santatowingservices.com',
				'home'         => array( 'title' => 'Santa Towing | 24/7 Towing, Haulage, Repairs & Rentals in Ghana', 'description' => 'Expert 24/7 emergency towing, nationwide haulage & heavy machinery transport, professional auto repairs, car sales, and premium car rentals in Ghana.' ),
				'services'     => array( 'title' => 'Comprehensive Automotive Services | Santa Towing', 'description' => 'Explore our complete range of professional automotive services including 24/7 towing, mechanical repairs, body works, car sales, and rentals.' ),
				'towing'       => array( 'title' => '24/7 Emergency Towing & Car Recovery in Ghana | Santa Towing', 'description' => 'Fast, reliable 24/7 emergency towing, flatbed recovery, and roadside assistance across Accra, Tema, Kumasi, and nationwide in Ghana.' ),
				'haulage'      => array( 'title' => 'Haulage & Heavy Equipment Transport Services in Ghana | Santa Towing', 'description' => 'Professional heavy haulage, flatbed freight, lowbed machinery transport, and container logistics across Ghana. Safe, insured, and reliable cargo transport.' ),
				'rental'       => array( 'title' => 'Premium Car Rental Services in Ghana | Santa Towing', 'description' => 'Rent reliable, fully insured sedans, SUVs, pickups, and luxury vehicles in Ghana. Flexible daily, weekly, and corporate monthly rates.' ),
				'sales'        => array( 'title' => 'Buy Certified New & Used Cars in Ghana | Santa Towing', 'description' => 'Browse our inspected inventory of brand new, foreign used, and Ghana used cars for sale with flexible financing and warranty options.' ),
				'repairs'      => array( 'title' => 'Expert Auto Repairs & Computer Diagnostics | Santa Towing', 'description' => 'Professional mechanical repairs, computer diagnostics, engine overhauls, brake servicing, and routine maintenance by certified technicians.' ),
				'bodyWorks'    => array( 'title' => 'Auto Body Works, Collision Repair & Spray Painting | Santa Towing', 'description' => 'Restore your vehicle to showroom condition with our professional dent repair, chassis straightening, and oven-baked spray painting services.' ),
				'parts'        => array( 'title' => 'Genuine Auto Parts, Batteries & Accessories | Santa Towing', 'description' => 'Shop original OEM and certified aftermarket car parts, heavy-duty batteries, tires, lubricants, and brake pads in Ghana.' ),
				'fleet'        => array( 'title' => 'Commercial Fleet Maintenance & Management | Santa Towing', 'description' => 'Dedicated B2B fleet maintenance, priority 24/7 towing, and cost-effective servicing solutions for corporate vehicles in Ghana.' ),
				'blog'         => array( 'title' => 'Automotive Blog & Resources | Santa Towing', 'description' => 'Read the latest automotive guides, towing tips, safety advice, and industry updates from the experts at Santa Towing.' ),
				'contact'      => array( 'title' => 'Contact Us | 24/7 Auto Support & Towing | Santa Towing', 'description' => 'Get in touch with Santa Towing for inquiries, service bookings, or emergency 24/7 towing assistance across Ghana.' ),
				'faqs'         => array( 'title' => 'Frequently Asked Questions | Santa Towing', 'description' => 'Find answers to common questions about our emergency towing, auto repair, car rental, and car sales services.' ),
				'gallery'      => array( 'title' => 'Our Work Gallery | Towing & Auto Repairs | Santa Towing', 'description' => 'View our gallery showcasing successful car recoveries, auto body restorations, repair works, and our premium rental fleet.' ),
				'careers'      => array( 'title' => 'Careers | Join the Santa Towing Team', 'description' => 'Explore exciting career opportunities for mechanics, tow truck operators, and administrative staff at Santa Towing.' ),
				'serviceAreas' => array( 'title' => 'Nationwide Towing Service Areas in Ghana | Santa Towing', 'description' => 'Explore our comprehensive towing and roadside assistance coverage across all 16 regions, major cities, towns, and highways in Ghana.' ),
			),
			'home' => array(
				'hero' => array(
					'slides' => array(
						array( 'id' => 1, 'headline' => '24/7 Emergency Towing Services', 'subheadline' => 'Fast response, safe car transport, affordability, and reliability. Call us anytime, anywhere.', 'image' => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ), 'imageAlt' => 'Santa Towing heavy duty recovery tow truck performing emergency highway assistance' ),
						array( 'id' => 2, 'headline' => 'Accident Recovery & Roadside Assistance', 'subheadline' => 'Professional winch-outs, flat tire changes, jump starts, and safe off-road recovery.', 'image' => stbm_asset_image( 'image-9-car-battery-maintenance.jpg' ), 'imageAlt' => 'Certified automotive technician inspecting car battery and electrical connections' ),
						array( 'id' => 3, 'headline' => 'Flatbed Towing & Car Transport', 'subheadline' => 'Secure and damage-free transport for luxury cars, SUVs, and specialized cars.', 'image' => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'imageAlt' => 'Mechanic pouring premium engine lubricant fluid during routine servicing' ),
						array( 'id' => 4, 'headline' => 'Premium Car Rentals', 'subheadline' => 'Choose from our wide range of well-maintained cars for business or leisure.', 'image' => stbm_asset_image( 'image-11-brake-parts-disk-maintenance.jpg' ), 'imageAlt' => 'High-performance brake rotor and caliper replacement in certified workshop' ),
						array( 'id' => 5, 'headline' => 'Professional Auto Repairs', 'subheadline' => 'Expert diagnostics and repairs by certified mechanics you can trust.', 'image' => stbm_asset_image( 'image-12-tyre-maintenance.jpg' ), 'imageAlt' => 'Automotive specialist checking tread depth and tyre pressure on modern wheel' ),
					),
				),
				'quickActions' => array(
					'items' => array(
						array( 'id' => 1, 'title' => 'Request Towing', 'description' => '24/7 emergency response', 'path' => '/towing' ),
						array( 'id' => 2, 'title' => 'Rent a Car', 'description' => 'Flexible daily & weekly rates', 'path' => '/rental' ),
						array( 'id' => 3, 'title' => 'Buy a Car', 'description' => 'New & premium used cars', 'path' => '/sales' ),
						array( 'id' => 4, 'title' => 'Call for Repairs', 'description' => 'Certified expert mechanics', 'path' => '/repairs' ),
						array( 'id' => 6, 'title' => 'Shop Auto Parts', 'description' => 'Genuine replacement parts', 'path' => '/parts' ),
					),
				),
				'about' => array(
					'badge'            => 'Welcome to Santa Towing',
					'heading'          => 'Leading Towing & Premium Automotive Care',
					'p1'               => 'With over 15 years of industry excellence, Santa Towing & Garage Services is your premier partner for 24/7 emergency towing, heavy car recovery, certified mechanical repairs, and premium car rentals.',
					'p2'               => 'Whether you are stranded on the motorway, looking to purchase a reliable vehicle, rent a car for your business trip, or need expert mechanical repairs, we are committed to delivering safety, professionalism, and affordability.',
					'discoverBtn'      => 'Discover Our Services',
					'callBtn'          => 'Call Hotline',
					'badgeTitle'       => 'Emergency Towing',
					'badgeSubtitle'    => 'Rapid Dispatch Team',
					'badgeDescription' => 'Nationwide roadside assistance with guaranteed fast arrival.',
				),
				'servicesSection' => array(
					'heading'     => 'Complete Towing & Automotive Solutions',
					'description' => 'From emergency towing to scheduled maintenance, our specialized team delivers reliable, high-standard automotive support across Ghana.',
					'viewAllBtn'  => 'View All Automotive Services',
				),
				'haulage' => array(
					'badge'       => 'Heavy Haulage Solutions',
					'heading'     => 'Nationwide Heavy Haulage & Industrial Transport',
					'description' => 'Comprehensive freight solutions, heavy machinery hauling, container transport, and oversized cargo logistics across Ghana and West Africa.',
					'ctaCall'     => 'Call for Haulage: 0244753849',
					'ctaWhatsApp' => 'WhatsApp Haulage Quote',
				),
				'featuredCars' => array(
					'heading'     => 'Featured Cars for Sale',
					'description' => 'Explore our vetted inventory of premium brand-new and certified pre-owned cars with complete mechanical inspection reports.',
					'viewAllBtn'  => 'View Full Car Inventory',
				),
				'rentalFleet' => array(
					'heading'     => 'Premium Car Rental Fleet',
					'description' => 'Clean, fully insured cars available for daily, weekly, or corporate monthly rentals with airport pickup options.',
				),
				'autoParts' => array(
					'heading'     => 'Genuine Auto Parts & Accessories',
					'description' => 'We stock thousands of high-quality OEM and verified aftermarket parts, batteries, and accessories to keep your car running at peak performance.',
					'browseBtn'   => 'Browse Full Catalog',
				),
				'whyChooseUs' => array(
					'badge'            => 'Why Santa Towing',
					'heading'          => 'Why Choose Santa Towing & Garage?',
					'description'      => "We don't just fix cars; we build lasting relationships based on trust, swift emergency response, quality workmanship, and transparent pricing.",
					'bannerHeading'    => 'Need Immediate Roadside Assistance or Repairs?',
					'bannerSubtext'    => 'Our 24/7 emergency dispatch and expert mechanical teams are standing by right now.',
					'requestOnlineBtn' => 'Request Tow Online',
				),
				'blogSection' => array(
					'heading'    => 'Latest News & Maintenance Insights',
					'viewAllBtn' => 'View All Articles',
				),
				'faqSection' => array(
					'heading'     => 'Frequently Asked Questions',
					'description' => 'Quick answers regarding our 24/7 towing response times, repair warranties, car rentals, and pricing.',
				),
				'bottomSections' => array(
					'appBadge'              => 'Mobile Dispatch App',
					'appHeading'            => 'Instant Roadside Rescue in the Palm of Your Hand',
					'appDescription'        => 'Experience the next generation of roadside assistance. Request emergency towing, track your flatbed in real-time, and get back on the road safely.',
					'ctaBannerHeading'      => 'Stranded On The Road? We Are Ready To Move.',
					'ctaBannerDescription'  => 'Our dispatch centers in Accra, Tema, Kumasi, and Takoradi are operational 24/7.',
					'callEmergency'         => 'Call 0244753849',
					'chatWhatsApp'          => 'WhatsApp 24/7',
				),
			),
			'towing' => array(
				'badge'           => '24/7 Emergency Assistance',
				'title'           => '24/7 Emergency Towing & Car Recovery',
				'description'     => 'Fast response, modern flatbed and wheel-lift towing, heavy equipment transport, and nationwide roadside assistance across Ghana.',
				'hotline'         => '0244753849',
				'averageTime'     => '15-30 Min Arrival Time',
				'calculatorTitle' => 'Instant Towing Rate Calculator',
				'quoteHeading'    => 'Need Immediate Towing Assistance?',
				'quoteSubheading' => 'Our dispatch team will connect you with the nearest tow truck instantly.',
			),
			'haulage' => array(
				'badge'       => 'Heavy Cargo & Logistics',
				'title'       => 'Heavy Haulage & Equipment Transport',
				'description' => 'Specialized heavy transport for construction machinery, industrial equipment, containers, and oversized loads nationwide in Ghana.',
				'callCta'     => 'Call for Haulage: 0244753849',
				'whatsappCta' => 'WhatsApp for Haulage',
			),
			'services' => array(
				'badge'       => 'Complete Automotive Solutions',
				'title'       => 'Our Professional Automotive Services',
				'description' => 'From 24/7 emergency roadside towing to major mechanical repairs, spray painting, car rentals, and parts supply in Ghana.',
				'callNow'     => 'Call Dispatch: 0244753849',
				'whatsappNow' => 'WhatsApp Us',
			),
			'sales' => array(
				'badge'       => 'Verified Vehicles',
				'title'       => 'Quality Cars For Sale',
				'description' => 'Explore certified new, foreign-used, and inspected Ghana-used cars with flexible financing and warranty options.',
			),
			'rental' => array(
				'badge'       => 'Flexible Car Rentals',
				'title'       => 'Car Rental Services in Ghana',
				'description' => 'Premium self-drive and chauffeur-driven rental cars for business, events, airport pickups, and long-term lease.',
			),
			'repairs' => array(
				'badge'       => 'Certified Mechanics',
				'title'       => 'Professional Auto Repairs & Diagnostics',
				'description' => 'Advanced computer diagnostics, engine rebuilds, transmission repairs, suspension overhaul, and routine maintenance.',
			),
			'bodyWorks' => array(
				'badge'       => 'Automotive Aesthetics',
				'title'       => 'Body Works, Dent Repair & Spray Painting',
				'description' => 'Factory-grade spray booth painting, computerized frame alignment, collision repair, and scratch restoration.',
			),
			'parts' => array(
				'badge'       => 'Genuine Auto Parts',
				'title'       => 'Original Auto Parts & Batteries',
				'description' => 'OEM replacement parts, premium car batteries, lubricants, brake components, and vehicle accessories with warranty.',
			),
			'fleet' => array(
				'badge'       => 'Corporate Solutions',
				'title'       => 'Commercial Fleet Maintenance & Management',
				'description' => 'Scheduled maintenance contracts, priority 24/7 emergency towing, and full fleet optimization for businesses in Ghana.',
				'heroImage'   => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
			),
			'contact' => array(
				'badge'       => 'Get in Touch',
				'title'       => "We're Here to Help - Anytime, Anywhere",
				'description' => 'Reach out for towing, repairs, rentals, car sales, or general enquiries. Our dedicated team is ready to assist you.',
				'phone'       => '0244753849',
				'address'     => '123 Automotive Avenue, Spintex Road, Accra, Ghana',
				'hours'       => '24 Hours / 7 Days a Week',
				'heroImage'   => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
				'heroImageAlt'=> 'Santa Towing customer support and 24/7 dispatch center',
			),
			'blog' => array(
				'badge'        => 'Insights & Guides',
				'title'        => 'Blog & Resources',
				'description'  => 'Expert advice, maintenance tips, safety guidelines, and company news to keep you moving safely on the road.',
				'heroImage'    => stbm_asset_image( 'image-15-car-lighting-maintenance.jpg' ),
				'heroImageAlt' => 'Automotive maintenance guides and road safety insights header banner',
				'posts'        => self::get_default_blog_posts(),
			),
			'faqs' => array(
				'badge'                   => 'Help & Support',
				'title'                   => 'Frequently Asked Questions',
				'description'             => 'Find quick answers about our towing, rental, repair, sales, and parts services.',
				'stillHaveQuestionsTitle' => 'Still Have Questions?',
				'stillHaveQuestionsText'  => "If you couldn't find the answer you were looking for, our customer support team is ready to help you out.",
			),
			'gallery' => array(
				'badge'        => 'Our Work in Action',
				'title'        => 'See Our Work in Action',
				'description'  => 'A visual look at our towing fleet, workshop facilities, car inventory, and completed repairs. Quality you can see.',
				'heroImage'    => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ),
				'heroImageAlt' => 'Photo gallery of completed roadside recoveries and automotive repair works',
				'beforeImage'  => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ),
				'afterImage'   => stbm_asset_image( 'image-9-car-battery-maintenance.jpg' ),
			),
			'careers' => array(
				'badge'        => 'Careers',
				'title'        => 'Build Your Career With Us',
				'description'  => "Join a team driven by excellence, innovation, and a passion for automotive service. We're looking for dedicated professionals to grow with us.",
				'heroImage'    => stbm_asset_image( 'image-13-filters-maintenance.jpg' ),
				'heroImageAlt' => 'Join Santa Towing automotive and roadside recovery professional team',
			),
			'policies' => array(
				'common'            => array( 'backToHome' => 'Back to Home', 'phoneLabel' => 'Phone', 'whatsappLabel' => 'WhatsApp Support', 'whatsappCta' => 'Chat on WhatsApp' ),
				'privacy'           => array( 'title' => 'Privacy Policy', 'subtitle' => 'How we collect, use, and protect your personal information when you use our services.' ),
				'terms'             => array( 'title' => 'Terms & Conditions', 'subtitle' => 'Please read these terms carefully before using our website or booking our automotive services.' ),
				'towingTerms'       => array( 'title' => 'Towing Service Terms & Conditions', 'subtitle' => 'Please read these terms carefully before requesting or using our towing and recovery services.' ),
				'cancellation'      => array( 'title' => 'Cancellation & Refund Policy', 'subtitle' => 'Please review our policies regarding service cancellations and refunds.' ),
				'pricing'           => array( 'title' => 'Pricing & Payment Policy', 'subtitle' => 'Please review our policies regarding service pricing and payments.' ),
				'damageLiability'   => array( 'title' => 'Car Damage & Liability Policy', 'subtitle' => 'Please review our policies regarding car condition, care, and liability during towing.' ),
				'emergencyService'  => array( 'title' => 'Emergency Service Policy', 'subtitle' => 'Please review our policies regarding emergency towing, roadside recovery, and urgent dispatch.' ),
				'serviceAreaPolicy' => array( 'title' => 'Service Area & Availability Policy', 'subtitle' => 'Please review our policies regarding geographic coverage, operating hours, and service availability.' ),
				'disclaimer'        => array( 'title' => 'Website & Service Disclaimer', 'subtitle' => 'Important legal notices regarding the information and calculators on our website.' ),
				'cookie'            => array( 'title' => 'Cookie Policy', 'subtitle' => 'Information about how our website uses cookies and similar technologies.' ),
				'complaints'        => array( 'title' => 'Customer Complaints & Dispute Resolution Policy', 'subtitle' => 'How to submit feedback, report a service issue, or resolve a billing dispute.' ),
				'accessibility'     => array( 'title' => 'Accessibility Statement', 'subtitle' => 'Our commitment to ensuring digital accessibility for all users.' ),
			),
			'serviceAreas' => array(
				'title'       => 'Our Service Areas Across Ghana',
				'description' => 'Fast, dependable 24/7 emergency towing, vehicle recovery, and roadside assistance across all 16 regions, major cities, and highway corridors in Ghana.',
				'template'    => array(
					'breadcrumbParent' => 'Towing Services',
					'breadcrumbIndex'  => 'Service Areas',
					'ctaHeading'       => 'Need Immediate Assistance?',
					'ctaSubtext'       => 'Call or WhatsApp us now to book your tow.',
					'callButton'       => 'Call Now',
					'whatsappButton'   => 'WhatsApp Us',
					'prevAreaLabel'    => 'Previous Area',
					'nextAreaLabel'    => 'Next Area',
				),
				'overrides'   => new stdClass(),
			),
			'images' => array(
				'alt' => array(
					'welcomeTowing'       => 'Santa Towing state-of-the-art repair workshop and dispatch facility in Accra',
					'mobileScreen'        => 'Santa Towing mobile app screenshot showing live GPS driver tracking',
					'towingVehicle'       => 'Modern hydraulic flatbed recovery vehicle with emergency lights on highway',
					'heavyRecovery'       => 'Commercial heavy duty winching crane recovering stranded bus',
					'constructionHaulage' => 'Heavy lowbed transporter carrying industrial yellow machinery equipment across Ghana',
					'suvKiaSportage'      => 'Brand new 2024 Kia Sportage SUV available for daily and executive corporate hire',
					'sedanCorolla'        => 'Clean automatic Toyota Corolla sedan for business trips and personal car rental',
					'vanHyundai'          => '12-passenger Hyundai H1 commercial van for corporate transport and family travel',
					'economyNissan'       => 'Fuel-efficient Nissan Almera economy rental car with cold air-conditioning',
					'luxuryMercedes'      => 'Mercedes-Benz luxury vehicle with chauffeur for weddings, diplomats, and VIP events',
					'pickupHilux'         => 'Rugged 2023 Toyota Hilux double cabin 4WD pickup truck for rough terrain travel',
					'batteryMaintenance'  => 'Heavy duty car battery load testing and replacement in workshop',
					'engineFluids'        => 'Automotive technician changing engine oil and inspecting transmission fluid',
					'brakeParts'          => 'New ventilated brake discs and hydraulic brake pads mounted on wheel hub',
					'tyreMaintenance'     => 'Automated computerized tyre balancing and wheel alignment machine',
					'filtersMaintenance'  => 'Genuine automotive oil, fuel, and air filter replacements',
					'suspensionParts'     => 'Heavy duty coil springs and gas-pressurized shock absorbers',
					'carLighting'         => 'LED headlight assembly and electrical bulbs replacement stock',
					'autoAccessories'     => 'Safety warning triangles, jump leads, tire inflators and car emergency accessories',
				),
			),
		);
	}

	/**
	 * Ensure `santa_towing_site_content` option exists on activation.
	 */
	public static function ensure_site_content_seeded() {
		$existing = get_option( self::OPTION_SITE_CONTENT, null );
		if ( null === $existing || ! is_array( $existing ) || empty( $existing ) ) {
			update_option( self::OPTION_SITE_CONTENT, self::get_default_site_content() );
		}
	}

	/**
	 * Recursively resolve legacy `/src/assets/images/...` tokens inside site_content to valid plugin/media URLs.
	 *
	 * @param mixed $node Data node.
	 * @return mixed
	 */
	public static function normalize_content_urls( $node ) {
		if ( is_string( $node ) ) {
			if ( 0 === strpos( $node, '/src/assets/images/' ) ) {
				return stbm_resolve_media_url( $node );
			}
			return $node;
		}
		if ( is_array( $node ) ) {
			foreach ( $node as $k => $v ) {
				$node[ $k ] = self::normalize_content_urls( $v );
			}
		}
		return $node;
	}

	/**
	 * Deep merge arrays preserving sequential lists from override.
	 *
	 * @param array $base     Base structure.
	 * @param array $override Saved overrides.
	 * @return array
	 */
	public static function deep_merge_content( $base, $override ) {
		if ( ! is_array( $override ) ) {
			return $base;
		}
		$result = $base;
		foreach ( $override as $k => $v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) && is_array( $result[ $k ] ) ) {
				// Sequential lists (numeric keys 0..n) replace wholesale instead of element-merging.
				if ( array_keys( $v ) === range( 0, count( $v ) - 1 ) || empty( $v ) ) {
					$result[ $k ] = $v;
				} else {
					$result[ $k ] = self::deep_merge_content( $result[ $k ], $v );
				}
			} else {
				$result[ $k ] = $v;
			}
		}
		return $result;
	}

	/**
	 * Get merged normalized site content array.
	 *
	 * @return array
	 */
	public static function get_site_content() {
		$defaults = self::get_default_site_content();
		$saved    = get_option( self::OPTION_SITE_CONTENT, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$merged = self::deep_merge_content( $defaults, $saved );
		return self::normalize_content_urls( $merged );
	}

	/**
	 * Save multiple dot-path overrides into `santa_towing_site_content`.
	 *
	 * @param array $updates Map of `dot.path => value`.
	 * @return array Full updated site content.
	 */
	public static function save_content_paths( $updates ) {
		$content = self::get_site_content();
		foreach ( $updates as $dot_key => $value ) {
			self::set_by_dot( $content, (string) $dot_key, $value );
		}
		update_option( self::OPTION_SITE_CONTENT, $content );
		return $content;
	}

	/**
	 * Reset a single dot-path key back to its default value.
	 *
	 * @param string $dot_key Dot path.
	 * @return array Full updated site content.
	 */
	public static function reset_content_path( $dot_key ) {
		$defaults = self::get_default_site_content();
		$content  = self::get_site_content();
		$found    = self::get_by_dot( $defaults, $dot_key, '__STBM_MISSING__' );
		if ( '__STBM_MISSING__' !== $found ) {
			self::set_by_dot( $content, $dot_key, $found );
		} else {
			self::unset_by_dot( $content, $dot_key );
		}
		update_option( self::OPTION_SITE_CONTENT, $content );
		return $content;
	}

	/**
	 * Reset all site content (and optionally reset inventory) back to factory defaults.
	 *
	 * @param bool $include_inventory Also reseed CPT inventory if requested.
	 * @return array
	 */
	public static function factory_reset_content( $include_inventory = false ) {
		$defaults = self::get_default_site_content();
		update_option( self::OPTION_SITE_CONTENT, $defaults );
		if ( $include_inventory ) {
			foreach ( array( 'cars', 'rentals', 'parts', 'gallery', 'faqs', 'careers' ) as $type ) {
				self::reset_inventory_type( $type );
			}
		}
		return $defaults;
	}

	/**
	 * Set nested array value using dot notation.
	 */
	public static function set_by_dot( &$arr, $path, $value ) {
		$parts = explode( '.', (string) $path );
		$ref   =& $arr;
		foreach ( $parts as $i => $part ) {
			$key = ctype_digit( $part ) ? (int) $part : $part;
			if ( $i === count( $parts ) - 1 ) {
				$ref[ $key ] = $value;
			} else {
				if ( ! isset( $ref[ $key ] ) || ! is_array( $ref[ $key ] ) ) {
					$ref[ $key ] = array();
				}
				$ref =& $ref[ $key ];
			}
		}
	}

	/**
	 * Read nested value using dot notation.
	 */
	public static function get_by_dot( $arr, $path, $default = null ) {
		$parts = explode( '.', (string) $path );
		$ref   = $arr;
		foreach ( $parts as $part ) {
			$key = ( is_array( $ref ) && ctype_digit( $part ) && array_key_exists( (int) $part, $ref ) ) ? (int) $part : $part;
			if ( ! is_array( $ref ) || ! array_key_exists( $key, $ref ) ) {
				return $default;
			}
			$ref = $ref[ $key ];
		}
		return $ref;
	}

	/**
	 * Remove nested key using dot notation.
	 */
	public static function unset_by_dot( &$arr, $path ) {
		$parts = explode( '.', (string) $path );
		$ref   =& $arr;
		$last  = count( $parts ) - 1;
		foreach ( $parts as $i => $part ) {
			$key = ctype_digit( $part ) ? (int) $part : $part;
			if ( ! is_array( $ref ) || ! array_key_exists( $key, $ref ) ) {
				return;
			}
			if ( $i === $last ) {
				unset( $ref[ $key ] );
				return;
			}
			$ref =& $ref[ $key ];
		}
	}

	/**
	 * Register CPTs and taxonomies ONLY if not already registered (for standalone operation without Core plugin).
	 */
	public static function maybe_register_standalone_cpts() {
		$cpts = array(
			'st_car'          => array( 'label' => 'Cars for Sale', 'singular' => 'Car', 'slug' => 'cars' ),
			'st_rental'       => array( 'label' => 'Rental Fleet', 'singular' => 'Rental Vehicle', 'slug' => 'rentals' ),
			'st_part'         => array( 'label' => 'Auto Parts', 'singular' => 'Auto Part', 'slug' => 'auto-parts' ),
			'st_gallery'      => array( 'label' => 'Work Gallery', 'singular' => 'Gallery Item', 'slug' => 'work-gallery' ),
			'st_faq'          => array( 'label' => 'FAQs', 'singular' => 'FAQ', 'slug' => 'faq-items' ),
			'st_career'       => array( 'label' => 'Careers', 'singular' => 'Career', 'slug' => 'career-openings' ),
			'st_service_area' => array( 'label' => 'Service Areas', 'singular' => 'Service Area', 'slug' => 'service-areas' ),
		);

		foreach ( $cpts as $pt => $cfg ) {
			if ( ! post_type_exists( $pt ) ) {
				register_post_type(
					$pt,
					array(
						'labels'       => array(
							'name'          => $cfg['label'],
							'singular_name' => $cfg['singular'],
						),
						'public'       => true,
						'show_ui'      => false,
						'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
						'has_archive'  => false,
						'rewrite'      => array( 'slug' => $cfg['slug'], 'with_front' => false ),
						'show_in_rest' => true,
					)
				);
			}
		}

		$taxes = array(
			'st_car_category'     => array( 'st_car', 'Car Categories' ),
			'st_rental_category'  => array( 'st_rental', 'Rental Categories' ),
			'st_part_category'    => array( 'st_part', 'Part Categories' ),
			'st_part_brand'       => array( 'st_part', 'Part Brands' ),
			'st_gallery_category' => array( 'st_gallery', 'Gallery Categories' ),
			'st_faq_category'     => array( 'st_faq', 'FAQ Categories' ),
			'st_department'       => array( 'st_career', 'Departments' ),
			'st_region'           => array( 'st_service_area', 'Regions' ),
		);
		foreach ( $taxes as $tax => $cfg ) {
			if ( ! taxonomy_exists( $tax ) ) {
				register_taxonomy(
					$tax,
					$cfg[0],
					array(
						'labels'       => array( 'name' => $cfg[1] ),
						'hierarchical' => true,
						'show_ui'      => false,
						'show_in_rest' => true,
					)
				);
			}
		}
	}

	/**
	 * Map inventory tab key to WordPress CPT.
	 *
	 * @param string $type Inventory key (`cars`|`rentals`|`parts`|`gallery`|`faqs`|`careers`).
	 * @return string|false
	 */
	public static function map_inventory_cpt( $type ) {
		$map = array(
			'cars'    => 'st_car',
			'rentals' => 'st_rental',
			'parts'   => 'st_part',
			'gallery' => 'st_gallery',
			'faqs'    => 'st_faq',
			'careers' => 'st_career',
		);
		return $map[ $type ] ?? false;
	}

	/**
	 * Seed default datasets matching `src/data/inventoryDefaults.ts`.
	 *
	 * @param string $type Inventory type.
	 * @return array[]
	 */
	public static function get_default_inventory( $type ) {
		switch ( $type ) {
			case 'cars':
				return array(
					array(
						'id' => 1, 'slug' => '2024-luxury-sedan', 'name' => '2024 Luxury Sedan', 'type' => 'New', 'price' => 'GH₵ 540,000', 'year' => 2024, 'fuel' => 'Hybrid', 'transmission' => 'Automatic', 'mileage' => '0 km',
						'image' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ),
						'images' => array(
							array( 'url' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'alt' => '2024 Luxury Sedan Front Angle' ),
							array( 'url' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'alt' => '2024 Luxury Sedan Side Profile' ),
							array( 'url' => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'alt' => 'Engine Bay Inspection' ),
						),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Pristine 2024 luxury hybrid sedan offering outstanding fuel efficiency, refined executive leather comfort, and intelligent driver assist technologies tuned for Ghana roads.',
						'specs' => array(
							'engine'      => array( array( 'label' => 'Engine Capacity', 'value' => '2.0L Hybrid 4-Cylinder' ), array( 'label' => 'Horsepower', 'value' => '208 hp @ 5,700 rpm' ), array( 'label' => 'Fuel System', 'value' => 'Direct Multi-Point Injection' ) ),
							'performance' => array( array( 'label' => 'Acceleration (0-100 km/h)', 'value' => '7.2 seconds' ), array( 'label' => 'Transmission', 'value' => 'Electronic Continuously Variable (e-CVT)' ), array( 'label' => 'Drivetrain', 'value' => 'Front-Wheel Drive (FWD)' ) ),
							'dimensions'  => array( array( 'label' => 'Seating Capacity', 'value' => '5 Passengers' ), array( 'label' => 'Fuel Tank', 'value' => '50 Litres' ) ),
							'features'    => array( 'Apple CarPlay & Android Auto', 'Adaptive Radar Cruise Control', 'Wireless Phone Charging Pad', 'Dual-Zone Climate Control' ),
						),
						'seoTitle' => '2024 Luxury Sedan for Sale | Santa Towing Ghana',
						'seoDescription' => 'Buy brand new 2024 Luxury Sedan in Ghana. Price: GH₵ 540,000. Verified 150-point inspection and warranty at Santa Towing.',
					),
					array(
						'id' => 2, 'slug' => '2024-premium-suv', 'name' => '2024 Premium SUV', 'type' => 'New', 'price' => 'GH₵ 780,000', 'year' => 2024, 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'mileage' => '25 km',
						'image' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'alt' => '2024 Premium SUV Front' ), array( 'url' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'alt' => 'Interior Showcase' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Versatile, high-ground-clearance compact luxury SUV featuring panoramic moonroof, AWD all-weather traction, and premium digital cockpit.',
						'specs' => array(
							'engine'      => array( array( 'label' => 'Engine Capacity', 'value' => '2.5L 4-Cylinder Turbo' ), array( 'label' => 'Horsepower', 'value' => '187 hp @ 6,100 rpm' ) ),
							'performance' => array( array( 'label' => 'Drivetrain', 'value' => 'All-Wheel Drive (AWD)' ), array( 'label' => 'Transmission', 'value' => '8-Speed Automatic' ) ),
							'features'    => array( 'Panoramic Power Sunroof', 'Heated Front Seats', 'Surround 360-degree Parking Cameras' ),
						),
						'seoTitle' => '2024 Premium SUV for Sale in Accra | Santa Towing',
						'seoDescription' => 'Brand new 2024 Premium SUV available now with full GRA port customs clearance and 3-month mechanical guarantee.',
					),
					array(
						'id' => 3, 'slug' => '2024-executive-pickup', 'name' => '2024 Executive Pickup', 'type' => 'New', 'price' => 'GH₵ 620,000', 'year' => 2024, 'fuel' => 'Diesel', 'transmission' => 'Automatic', 'mileage' => '15 km',
						'image' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'alt' => '2024 Executive Pickup Side View' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Heavy duty, go-anywhere dual-cab 4x4 pickup engineered for tough construction sites, mining routes, and executive comfort.',
						'specs' => array(
							'engine'      => array( array( 'label' => 'Engine Capacity', 'value' => '2.8L Turbo Diesel' ) ),
							'performance' => array( array( 'label' => 'Drivetrain', 'value' => 'Selectable 4WD with Rear Diff Lock' ) ),
							'features'    => array( 'Heavy Duty Tow Hook Package', 'Leather Seats', 'Touchscreen Navigation' ),
						),
						'seoTitle' => '2024 Executive 4x4 Pickup for Sale | Santa Towing',
						'seoDescription' => 'Brand new 2024 4x4 turbo diesel pickup truck in Ghana. Heavy duty reliability guaranteed.',
					),
					array(
						'id' => 4, 'slug' => '2024-passenger-van', 'name' => '2024 Passenger Van', 'type' => 'New', 'price' => 'GH₵ 690,000', 'year' => 2024, 'fuel' => 'Diesel', 'transmission' => 'Manual', 'mileage' => '40 km',
						'image' => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'alt' => '2024 Passenger Van 12 Seater' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Spacious 12-passenger transport van ideal for corporate shuttles, tour operators, and airport transit operations.',
						'seoTitle' => '2024 12-Seater Passenger Van for Sale | Santa Towing',
						'seoDescription' => 'Brand new 12-seater commercial passenger van with cold dual A/C and high-roof cabin.',
					),
					array(
						'id' => 5, 'slug' => '2020-midsize-sedan', 'name' => '2020 Midsize Sedan', 'type' => 'Foreign Used', 'price' => 'GH₵ 264,000', 'year' => 2020, 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'mileage' => '72,000 km',
						'image' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'alt' => '2020 Midsize Sedan' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Freshly imported foreign-used sedan in mint condition. Low mileage, zero accident history, and ice-cold air conditioning.',
						'seoTitle' => '2020 Midsize Sedan Foreign Used | Santa Towing',
						'seoDescription' => 'Clean foreign used 2020 Sedan. Inspected and registered at Tema Port with genuine documentation.',
					),
					array(
						'id' => 6, 'slug' => '2019-luxury-suv', 'name' => '2019 Luxury SUV', 'type' => 'Foreign Used', 'price' => 'GH₵ 450,000', 'year' => 2019, 'fuel' => 'Diesel', 'transmission' => 'Automatic', 'mileage' => '65,000 km',
						'image' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'alt' => '2019 Luxury SUV Exterior' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Executive family SUV with full leather interior, premium sound system, and verified service logbook.',
						'seoTitle' => '2019 Luxury SUV Foreign Used Ghana | Santa Towing',
						'seoDescription' => 'Foreign used luxury SUV in Accra. GH₵ 450,000 with flexible bank financing available.',
					),
					array(
						'id' => 7, 'slug' => '2021-premium-hatchback', 'name' => '2021 Premium Hatchback', 'type' => 'Foreign Used', 'price' => 'GH₵ 210,000', 'year' => 2021, 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'mileage' => '48,000 km',
						'image' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'alt' => '2021 Premium Hatchback' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Economical city commuter hatchback with ultra-low fuel consumption, modern infotainment, and easy parking footprint.',
						'seoTitle' => '2021 Premium Hatchback Foreign Used | Santa Towing',
						'seoDescription' => 'Compact and fuel-efficient foreign used hatchback. Ideal for daily commuting in Accra and Kumasi.',
					),
					array(
						'id' => 8, 'slug' => '2018-heavy-duty-pickup', 'name' => '2018 Heavy Duty Pickup', 'type' => 'Foreign Used', 'price' => 'GH₵ 340,000', 'year' => 2018, 'fuel' => 'Diesel', 'transmission' => 'Manual', 'mileage' => '92,000 km',
						'image' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'alt' => '2018 Heavy Duty Pickup Truck' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Rugged 4WD workhorse pickup truck with reinforced chassis and cargo bed liner. Work-ready immediately.',
						'seoTitle' => '2018 Heavy Duty Pickup Truck Foreign Used | Santa Towing',
						'seoDescription' => 'Reliable 4WD turbo diesel pickup truck for business and commercial haulage.',
					),
					array(
						'id' => 9, 'slug' => '2018-compact-hatchback', 'name' => '2018 Compact Hatchback', 'type' => 'Ghana Used', 'price' => 'GH₵ 144,000', 'year' => 2018, 'fuel' => 'Petrol', 'transmission' => 'Manual', 'mileage' => '136,000 km',
						'image' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'alt' => '2018 Compact Hatchback Ghana Used' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Single owner registered car in Ghana. Well maintained with regular service records at Santa Towing workshop.',
						'seoTitle' => '2018 Compact Hatchback Ghana Used | Santa Towing',
						'seoDescription' => 'Affordable registered used hatchback with clean title and verified roadworthiness.',
					),
					array(
						'id' => 10, 'slug' => '2015-family-suv', 'name' => '2015 Family SUV', 'type' => 'Ghana Used', 'price' => 'GH₵ 185,000', 'year' => 2015, 'fuel' => 'Petrol', 'transmission' => 'Automatic', 'mileage' => '185,000 km',
						'image' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'alt' => '2015 Family SUV' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Comfortable 7-seater family SUV with working air conditioning, sound engine, and smooth transmission shifting.',
						'seoTitle' => '2015 Family SUV Ghana Used | Santa Towing',
						'seoDescription' => 'Spacious family SUV with clean interior and affordable maintenance parts available in Ghana.',
					),
					array(
						'id' => 11, 'slug' => '2017-commuter-sedan', 'name' => '2017 Commuter Sedan', 'type' => 'Ghana Used', 'price' => 'GH₵ 120,000', 'year' => 2017, 'fuel' => 'Petrol', 'transmission' => 'Manual', 'mileage' => '162,000 km',
						'image' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'alt' => '2017 Commuter Sedan' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'High reliability commuter vehicle. Excellent mechanical condition and very economical to run daily.',
						'seoTitle' => '2017 Commuter Sedan Ghana Used | Santa Towing',
						'seoDescription' => 'Quality inspected Ghana used sedan priced affordably at GH₵ 120,000.',
					),
					array(
						'id' => 12, 'slug' => '2014-utility-pickup', 'name' => '2014 Utility Pickup', 'type' => 'Ghana Used', 'price' => 'GH₵ 165,000', 'year' => 2014, 'fuel' => 'Diesel', 'transmission' => 'Manual', 'mileage' => '210,000 km',
						'image' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'alt' => '2014 Utility Pickup' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Durable commercial diesel utility truck. Engine recently overhauled with warranty.',
						'seoTitle' => '2014 Utility Diesel Pickup Ghana Used | Santa Towing',
						'seoDescription' => 'Workhorse utility pickup truck ready for immediate commercial operations.',
					),
				);

			case 'rentals':
				return array(
					array(
						'id' => 1, 'slug' => 'corolla-sedan', 'categorySlug' => 'sedan', 'name' => 'Toyota Corolla 2023', 'type' => 'Sedan', 'category' => 'Sedan',
						'price' => 'GH₵ 600 / day', 'daily' => 'GH₵ 600', 'weekly' => 'GH₵ 3,800', 'monthly' => 'GH₵ 14,000', 'seats' => 5, 'transmission' => 'Automatic', 'fuel' => 'Petrol',
						'image' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'alt' => 'Toyota Corolla 2023 Rental Front' ), array( 'url' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'alt' => 'Interior Comfort' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Reliable, fuel-efficient modern sedan ideal for executive airport pickups, business meetings, and city commuting in Accra.',
						'features' => array( 'Comprehensive Fleet Insurance Coverage', 'Unlimited Mileage in Greater Accra', 'Chauffeur Available on Request', 'Bluetooth & USB Charging' ),
						'seoTitle' => 'Toyota Corolla 2023 Rental in Ghana | Santa Towing Car Hire',
						'seoDescription' => 'Rent Toyota Corolla in Accra from GH₵ 600/day. Self-drive and chauffeur options with 24/7 breakdown support.',
					),
					array(
						'id' => 2, 'slug' => 'sportage-suv', 'categorySlug' => 'suv', 'name' => 'Kia Sportage 2024', 'type' => 'SUV', 'category' => 'SUV',
						'price' => 'GH₵ 900 / day', 'daily' => 'GH₵ 900', 'weekly' => 'GH₵ 5,800', 'monthly' => 'GH₵ 21,000', 'seats' => 5, 'transmission' => 'Automatic', 'fuel' => 'Petrol',
						'image' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'alt' => 'Kia Sportage 2024 SUV' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Spacious all-wheel drive SUV with premium comfort, high clearance, and ample boot space for family travel across Ghana.',
						'features' => array( 'All-Wheel Drive Performance', 'Panoramic Roof & Dual Climate Control', 'Full Comprehensive Insurance', 'Complimentary Child Seat upon request' ),
						'seoTitle' => 'Kia Sportage 2024 SUV Rental Ghana | Santa Towing',
						'seoDescription' => 'Premium SUV hire in Accra & Kumasi. Reliable AWD comfort for travel across Ghana.',
					),
					array(
						'id' => 3, 'slug' => 'hilux-pickup', 'categorySlug' => 'pickup', 'name' => 'Toyota Hilux 2023 4x4', 'type' => 'Pickup', 'category' => 'Pickup',
						'price' => 'GH₵ 800 / day', 'daily' => 'GH₵ 800', 'weekly' => 'GH₵ 5,000', 'monthly' => 'GH₵ 18,000', 'seats' => 5, 'transmission' => 'Automatic', 'fuel' => 'Diesel',
						'image' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-7-pickup-toyota-hilux-2023.jpg' ), 'alt' => 'Toyota Hilux 4x4 Pickup' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Rugged four-wheel drive double cabin pickup truck specifically suited for rugged terrains, mining sites, and cross-country expeditions.',
						'features' => array( 'Heavy Duty 4x4 Off-Road Drive', 'Spacious Cargo Bed with Tie-Down Points', 'Corporate Long-Term Discount Rates' ),
						'seoTitle' => 'Toyota Hilux 4x4 Rental Ghana | Santa Towing',
						'seoDescription' => 'Rent Toyota Hilux 4x4 pickup in Accra. High durability for construction and remote terrain travel.',
					),
					array(
						'id' => 4, 'slug' => 'mercedes-luxury', 'categorySlug' => 'luxury', 'name' => 'Mercedes-Benz E-Class', 'type' => 'Luxury', 'category' => 'Luxury',
						'price' => 'GH₵ 1,500 / day', 'daily' => 'GH₵ 1,500', 'weekly' => 'GH₵ 9,500', 'monthly' => 'GH₵ 35,000', 'seats' => 5, 'transmission' => 'Automatic', 'fuel' => 'Premium Petrol',
						'image' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'alt' => 'Mercedes-Benz E-Class Luxury Rental' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Executive VIP transportation and wedding luxury sedan with uniformed chauffeur service and VIP airport escort.',
						'features' => array( 'Professional Uniformed Chauffeur Included', 'Burmester Premium Surround Sound', 'Executive Rear Climate Control & Sunshades' ),
						'seoTitle' => 'Mercedes-Benz Luxury Car Rental Accra | Santa Towing',
						'seoDescription' => 'Executive VIP Mercedes rental for weddings, summits, and diplomatic travel in Ghana.',
					),
					array(
						'id' => 5, 'slug' => 'hyundai-van', 'categorySlug' => 'van', 'name' => 'Hyundai H1 Executive Bus', 'type' => 'Van', 'category' => 'Van',
						'price' => 'GH₵ 1,100 / day', 'daily' => 'GH₵ 1,100', 'weekly' => 'GH₵ 7,000', 'monthly' => 'GH₵ 25,000', 'seats' => 12, 'transmission' => 'Manual', 'fuel' => 'Diesel',
						'image' => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'alt' => 'Hyundai H1 12-Seater Executive Van' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => '12-passenger minibus ideal for corporate delegates, tourist groups, event transport, and family trips.',
						'features' => array( 'Comfortable 12-Passenger High-Roof Seating', 'Powerful Front & Rear Cabin Air Conditioning', 'Spacious Rear Luggage Compartment' ),
						'seoTitle' => '12-Seater Van Rental in Accra | Santa Towing Bus Hire',
						'seoDescription' => 'Spacious 12-seater passenger van rental for corporate groups and family events in Ghana.',
					),
					array(
						'id' => 6, 'slug' => 'nissan-economy', 'categorySlug' => 'economy', 'name' => 'Nissan Almera 2021', 'type' => 'Economy', 'category' => 'Economy',
						'price' => 'GH₵ 400 / day', 'daily' => 'GH₵ 400', 'weekly' => 'GH₵ 2,500', 'monthly' => 'GH₵ 9,000', 'seats' => 4, 'transmission' => 'Automatic', 'fuel' => 'Petrol',
						'image' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'alt' => 'Nissan Almera Economy Car' ) ),
						'status' => 'Available', 'hidden' => false,
						'description' => 'Budget-friendly economy sedan with low fuel consumption and great reliability for everyday city driving.',
						'features' => array( 'Affordable Daily & Weekly Rates', 'Low Fuel Consumption', 'Clean Interior with Strong Air Conditioning' ),
						'seoTitle' => 'Affordable Economy Car Rental Accra | Santa Towing',
						'seoDescription' => 'Rent Nissan Almera from GH₵ 400 per day. Clean, budget-friendly car hire in Accra.',
					),
				);

			case 'parts':
				return array(
					array(
						'id' => 1, 'slug' => 'premium-heavy-duty-battery-70ah', 'name' => 'Premium Heavy Duty Battery 70Ah', 'brand' => 'Bosch', 'category' => 'Car Batteries', 'price' => 'GH₵ 850', 'rating' => 5.0, 'reviews' => 124, 'inStock' => true, 'status' => 'In Stock', 'sku' => 'BSH-70AH-HD',
						'image' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'alt' => 'Bosch 70Ah Heavy Duty Battery' ), array( 'url' => stbm_asset_image( 'image-12-tyre-maintenance.jpg' ), 'alt' => 'Battery Maintenance' ) ),
						'specs' => array( array( 'label' => 'Voltage', 'value' => '12V' ), array( 'label' => 'Capacity', 'value' => '70Ah' ), array( 'label' => 'CCA', 'value' => '760A' ), array( 'label' => 'Terminal Type', 'value' => 'Standard Post' ) ),
						'compatibility' => 'Fits most modern sedans and mid-sized SUVs. Specifically designed for cars with start-stop technology and high electrical demands.',
						'warranty' => '24-Month Nationwide Replacement Warranty.',
						'installation' => 'Professional installation recommended. Memory saver tool required during replacement to prevent loss of car settings.',
						'description' => 'High-performance maintenance-free lead-acid battery engineered with heavy-duty grids for tropical heat resistance and fast recharging.',
						'hidden' => false,
						'seoTitle' => 'Bosch 70Ah Heavy Duty Battery Ghana | Santa Towing Auto Parts',
						'seoDescription' => 'Buy genuine Bosch 70Ah 12V Car Battery in Accra. 24-month replacement warranty and on-site testing available.',
					),
					array(
						'id' => 2, 'slug' => 'synthetic-motor-oil-5w-30-5l', 'name' => 'Synthetic Motor Oil 5W-30 (5L)', 'brand' => 'Castrol', 'category' => 'Engine Oil & Fluids', 'price' => 'GH₵ 420', 'rating' => 4.8, 'reviews' => 98, 'inStock' => true, 'status' => 'In Stock', 'sku' => 'CST-5W30-5L',
						'image' => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-10-engine-oil-fluids-maintenance.jpg' ), 'alt' => 'Castrol 5W-30 Synthetic Motor Oil Bottle' ), array( 'url' => stbm_asset_image( 'image-13-filters-maintenance.jpg' ), 'alt' => 'Oil and Filter Kit' ) ),
						'specs' => array( array( 'label' => 'Viscosity Grade', 'value' => 'SAE 5W-30' ), array( 'label' => 'Volume', 'value' => '5 Litres' ), array( 'label' => 'Oil Type', 'value' => '100% Full Synthetic' ) ),
						'compatibility' => 'Formulated for high thermal stability in tropical climates. Suitable for modern Japanese, European, and American passenger cars.',
						'warranty' => '100% Genuine OEM Sealed Bottle Guarantee.',
						'installation' => 'Always replace the oil filter during an engine oil service for optimal engine longevity.',
						'description' => 'Advanced full synthetic engine lubricant providing extreme wear protection, cleaner pistons, and superior fuel economy.',
						'hidden' => false,
						'seoTitle' => 'Castrol 5W-30 Synthetic Motor Oil 5L Ghana | Santa Towing',
						'seoDescription' => 'Original Castrol Edge 5W-30 synthetic motor oil. Genuine sealed stock with prompt delivery across Ghana.',
					),
					array(
						'id' => 3, 'slug' => 'ceramic-brake-pads-front', 'name' => 'Ceramic Brake Pads (Front)', 'brand' => 'Brembo', 'category' => 'Brake Pads & Discs', 'price' => 'GH₵ 650', 'rating' => 4.9, 'reviews' => 86, 'inStock' => true, 'status' => 'In Stock', 'sku' => 'BRM-CER-FR',
						'image' => stbm_asset_image( 'image-11-brake-parts-disk-maintenance.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-11-brake-parts-disk-maintenance.jpg' ), 'alt' => 'Brembo Ceramic Front Brake Pads' ) ),
						'specs' => array( array( 'label' => 'Material', 'value' => 'Premium Low-Dust Ceramic' ), array( 'label' => 'Axle Placement', 'value' => 'Front Axle (Left & Right)' ), array( 'label' => 'Certification', 'value' => 'ECE R90 Certified' ) ),
						'compatibility' => 'Engineered for smooth stopping power, zero brake squeal, and ultra-low brake dust generation.',
						'warranty' => '12-Month or 20,000 km Performance Warranty.',
						'installation' => 'Ensure brake rotors are resurfaced or checked for runout prior to pad installation.',
						'description' => 'Ultra-quiet ceramic friction formulation offering exceptional pedal feel, heat resistance, and long rotor life.',
						'hidden' => false,
						'seoTitle' => 'Brembo Ceramic Brake Pads Front Set | Santa Towing Parts',
						'seoDescription' => 'Genuine Brembo ceramic front brake pads. Low dust, zero squeal, and high temperature fade resistance.',
					),
					array(
						'id' => 4, 'slug' => 'all-season-tire-205-55r16', 'name' => 'All-Season Tire 205/55R16', 'brand' => 'Michelin', 'category' => 'Tires', 'price' => 'GH₵ 1,200', 'rating' => 5.0, 'reviews' => 142, 'inStock' => true, 'status' => 'In Stock', 'sku' => 'MCH-205-55-16',
						'image' => stbm_asset_image( 'image-12-tyre-maintenance.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-12-tyre-maintenance.jpg' ), 'alt' => 'Michelin 205/55R16 Tire Tread' ) ),
						'specs' => array( array( 'label' => 'Size', 'value' => '205/55R16' ), array( 'label' => 'Load Index', 'value' => '91 (615 kg max load)' ), array( 'label' => 'Speed Rating', 'value' => 'V (240 km/h max)' ) ),
						'compatibility' => 'Direct OEM fitment for Toyota Corolla, Honda Civic, Hyundai Elantra, VW Golf, and similar passenger sedans.',
						'warranty' => '5-Year Manufacturer Tread Life Warranty.',
						'installation' => 'Computerized wheel balancing and new valve stems provided upon fitting at our garage.',
						'description' => 'Premium highway touring tire engineered for wet road braking grip, long tread life, and low road noise.',
						'hidden' => false,
						'seoTitle' => 'Michelin 205/55R16 All-Season Tire Ghana | Santa Towing',
						'seoDescription' => 'High quality Michelin 205/55R16 tires in Accra with professional fitting and computerized wheel balancing.',
					),
					array(
						'id' => 5, 'slug' => 'engine-air-filter', 'name' => 'Engine Air Filter', 'brand' => 'K&N', 'category' => 'Filters', 'price' => 'GH₵ 350', 'rating' => 4.7, 'reviews' => 64, 'inStock' => true, 'status' => 'In Stock', 'sku' => 'KN-AIR-FLTR',
						'image' => stbm_asset_image( 'image-13-filters-maintenance.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-13-filters-maintenance.jpg' ), 'alt' => 'High Performance Engine Air Filter' ) ),
						'specs' => array( array( 'label' => 'Filter Media', 'value' => 'Multi-layer Cotton Gauze' ), array( 'label' => 'Filtration Efficiency', 'value' => '99% Dust & Particulate Capture' ) ),
						'compatibility' => 'Drops directly into OEM factory air boxes without modifications.',
						'warranty' => '1,000,000 Mile Limited Warranty.',
						'installation' => 'Simple 5-minute toolless DIY installation.',
						'description' => 'Reusable high-flow performance air filter designed to increase horsepower and protect engine internals from dust.',
						'hidden' => false,
						'seoTitle' => 'K&N Performance Engine Air Filter Ghana | Santa Towing',
						'seoDescription' => 'Washable high flow performance air filter for optimal engine horsepower and filtration in dusty environments.',
					),
					array(
						'id' => 6, 'slug' => 'led-headlight-bulbs-h7-pair', 'name' => 'LED Headlight Bulbs H7 (Pair)', 'brand' => 'Philips', 'category' => 'Lighting', 'price' => 'GH₵ 550', 'rating' => 4.6, 'reviews' => 53, 'inStock' => true, 'status' => 'In Stock', 'sku' => 'PHL-LED-H7',
						'image' => stbm_asset_image( 'image-15-car-lighting-maintenance.jpg' ),
						'images' => array( array( 'url' => stbm_asset_image( 'image-15-car-lighting-maintenance.jpg' ), 'alt' => 'Philips H7 LED Headlight Bulbs' ) ),
						'specs' => array( array( 'label' => 'Bulb Base', 'value' => 'H7 Standard Socket' ), array( 'label' => 'Color Temperature', 'value' => '6000K Crisp Cool White' ) ),
						'compatibility' => 'Plug-and-play replacement for factory halogen H7 low beam or high beam sockets with CANBus error-free drivers.',
						'warranty' => '2-Year Replacement Warranty.',
						'installation' => 'Ensure beam pattern is properly leveled to avoid blinding oncoming traffic.',
						'description' => 'High-output LED headlight conversion kit providing daylight white illumination, instant startup, and sharp cutoff.',
						'hidden' => false,
						'seoTitle' => 'Philips H7 LED Headlight Conversion Kit Ghana | Santa Towing',
						'seoDescription' => 'Upgrade your vehicle lighting with genuine Philips 6000K H7 LED bulbs. High beam visibility for night driving.',
					),
				);

			case 'gallery':
				return array(
					array( 'id' => 1, 'slug' => 'diagnostic-bay', 'category' => 'Workshop', 'url' => stbm_asset_image( 'image-3-sedan-toyota-corolla.jpg' ), 'title' => 'State-of-the-art diagnostic bay', 'alt' => 'Computerized automotive diagnostic bay in Accra', 'span' => 'col-span-1 row-span-2', 'description' => 'Advanced electronic diagnostic equipment used by certified mechanics to scan automotive engine, ABS, and transmission modules.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 2, 'slug' => 'heavy-duty-recovery-vehicle', 'category' => 'Towing Fleet', 'url' => stbm_asset_image( 'image-8-heavy-duty-recovery-vehicle.jpg' ), 'title' => 'Heavy-duty recovery vehicle', 'alt' => 'Heavy duty tow truck on highway recovery', 'span' => 'col-span-2 row-span-1', 'description' => 'Hydraulic heavy recovery boom unit rescuing commercial trucks and haulage trailers across inter-city highways.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 3, 'slug' => 'expert-mechanics-team', 'category' => 'Team & Facilities', 'url' => stbm_asset_image( 'image-4-van-hyundai-h1-2022.jpg' ), 'title' => 'Our expert mechanics', 'alt' => 'Santa Towing certified mechanical team in workshop', 'span' => 'col-span-1 row-span-1', 'description' => 'Certified automotive technicians conducting precision inspections and scheduled maintenance.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 4, 'slug' => 'premium-pre-owned-inventory', 'category' => 'Car Sales', 'url' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'title' => 'Premium pre-owned inventory', 'alt' => 'Luxury car lineup at showroom', 'span' => 'col-span-1 row-span-1', 'description' => 'Executive vehicle fleet prepared for handover after passing 150-point technical check.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 5, 'slug' => 'luxury-suv-rentals', 'category' => 'Rental Fleet', 'url' => stbm_asset_image( 'image-2-suv-kia-sportage-2024.png' ), 'title' => 'Luxury SUV rentals', 'alt' => 'All-wheel drive rental SUV ready for customer pickup', 'span' => 'col-span-1 row-span-2', 'description' => 'Cleaned and sanitized rental fleet ready for self-drive and chauffeur trips.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 6, 'slug' => 'custom-paint-spray-booth', 'category' => 'Body Works', 'url' => stbm_asset_image( 'image-5-economy-nissan-almera-2021.jpg' ), 'title' => 'Custom paint job', 'alt' => 'Dust-free oven-baked automotive spray booth finish', 'span' => 'col-span-2 row-span-2', 'description' => 'Factory-spec computerized paint mixing and oven-baked clear coat finish.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 7, 'slug' => 'engine-overhaul', 'category' => 'Workshop', 'url' => stbm_asset_image( 'image-6-luxury-mercedes-benz.jpg' ), 'title' => 'Engine overhaul in progress', 'alt' => 'Certified master technicians rebuilding engine', 'span' => 'col-span-1 row-span-1', 'description' => 'Complete mechanical engine refurbishment using OEM replacement components.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 8, 'slug' => 'flatbed-towing-operation', 'category' => 'Towing Fleet', 'url' => stbm_asset_image( 'image-1-towing-vehicle.jpg' ), 'title' => 'City-wide flatbed towing', 'alt' => 'Hydraulic tilt flatbed towing vehicle in Accra', 'span' => 'col-span-1 row-span-1', 'description' => 'Rapid emergency response flatbed carrier dispatch in Accra and Tema.', 'status' => 'Published', 'hidden' => false ),
				);

			case 'faqs':
				return array(
					array( 'id' => 1, 'slug' => 'emergency-response-time', 'category' => 'Towing', 'q' => 'What is your average response time for emergency towing?', 'a' => 'Within the Greater Accra and Ashanti regions, we guarantee a response time of 30-45 minutes. For areas outside major city limits, response times may vary depending on distance and highway traffic conditions.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 2, 'slug' => 'service-coverage-areas', 'category' => 'Towing', 'q' => 'What areas do your towing services cover?', 'a' => 'We offer nationwide coverage across all 16 regions of Ghana through our owned fleet and vetted partner network, with primary rapid-response zones in Accra, Tema, Kumasi, and Takoradi.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 3, 'slug' => 'standard-towing-cost', 'category' => 'Towing', 'q' => 'How much does a standard tow cost?', 'a' => 'Towing costs depend on the distance and the type of vehicle. Our base rate starts at GH₵ 250 within city limits. We provide upfront, transparent pricing before dispatching a truck.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 4, 'slug' => 'insurance-direct-billing', 'category' => 'Towing', 'q' => 'Do you work with insurance companies?', 'a' => 'Yes, we are approved by most major insurance providers in Ghana. If you have roadside assistance coverage, we can often bill your insurance directly.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 5, 'slug' => 'lowered-modified-cars', 'category' => 'Towing', 'q' => 'Can you tow lowered or modified cars?', 'a' => 'Absolutely. We operate hydraulic flatbed tow trucks equipped with low-angle ramps specifically designed to safely transport modified, sports, or lowered luxury cars without bumper damage.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 6, 'slug' => 'rental-eligibility-requirements', 'category' => 'Rentals', 'q' => 'What are the eligibility requirements to rent a car?', 'a' => 'You must be at least 21 years old (25 for luxury or SUV classes), possess a valid Ghanaian driver’s license or an International Driving Permit, and provide a valid ID and security deposit.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 7, 'slug' => 'security-deposit-policy', 'category' => 'Rentals', 'q' => 'Is a security deposit required for rentals?', 'a' => 'Yes, a refundable security deposit is required for all rentals. The amount varies based on the vehicle category and is refunded within 3-5 business days after return in good order.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 8, 'slug' => 'rental-cancellation-policy', 'category' => 'Rentals', 'q' => 'What is your rental cancellation policy?', 'a' => 'Cancellations made 48 hours prior to the pickup time are fully refunded. Cancellations within 48 hours may incur a one-day rental charge.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 9, 'slug' => 'chauffeur-driver-service', 'category' => 'Rentals', 'q' => 'Can I rent a car with a professional chauffeur?', 'a' => 'Yes, we offer chauffeur services for an additional daily fee. All our drivers are professionally trained, vetted, uniformed, and familiar with inter-city travel routes.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 10, 'slug' => 'rental-mileage-limits', 'category' => 'Rentals', 'q' => 'Are there mileage limits on rentals?', 'a' => 'Most standard rentals come with unlimited mileage within Greater Accra. Cross-country journeys or long-term commercial rentals receive generous daily mileage allowances.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 11, 'slug' => 'standard-service-duration', 'category' => 'Repairs', 'q' => 'How long does a standard vehicle service take?', 'a' => 'A routine maintenance service (oil change, filter replacement, fluid top-up, and multipoint inspection) typically takes 1 to 2 hours if you have a scheduled appointment.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 12, 'slug' => 'repair-warranty-period', 'category' => 'Repairs', 'q' => 'Do you offer a warranty on mechanical repairs?', 'a' => 'Yes, all our mechanical repairs and labor come with a minimum 6-month or 10,000 km warranty. Parts warranties depend on the manufacturer but typically range from 1 to 2 years.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 13, 'slug' => 'oem-vs-aftermarket-parts', 'category' => 'Repairs', 'q' => 'Do you use genuine OEM parts for repairs?', 'a' => 'We offer the choice between genuine OEM (Original Equipment Manufacturer) parts and high-quality, certified aftermarket alternatives to suit your budget.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 14, 'slug' => 'loaner-cars-during-service', 'category' => 'Repairs', 'q' => 'Can I get a loaner car while my car is being repaired?', 'a' => 'Loaner cars are available subject to availability and the nature of the repair. Priority is given to fleet partners and customers undergoing major engine overhauls.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 15, 'slug' => 'computerized-diagnostic-reports', 'category' => 'Repairs', 'q' => 'Do you provide printed diagnostic reports?', 'a' => 'Yes, every computerized diagnostic scan comes with a detailed, easy-to-understand printed or digital report outlining fault codes and recommended solutions.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 16, 'slug' => 'car-financing-partners', 'category' => 'Sales', 'q' => 'Do you offer vehicle financing or installment options?', 'a' => 'Yes, we partner with leading Ghanaian banks and financial institutions to offer flexible auto loan options with competitive interest rates and terms up to 48 months.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 17, 'slug' => 'pre-purchase-inspection-process', 'category' => 'Sales', 'q' => 'Are your used cars inspected before being listed?', 'a' => 'Absolutely. Every pre-owned vehicle undergoes a rigorous 150-point mechanical, electrical, and structural inspection before being listed for sale.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 18, 'slug' => 'trade-in-evaluation', 'category' => 'Sales', 'q' => 'Can I trade in my current vehicle?', 'a' => 'Yes, we accept trade-ins. Bring your car in for a complimentary appraisal, and the value can be applied directly toward your new purchase.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 19, 'slug' => 'custom-vehicle-importation', 'category' => 'Sales', 'q' => 'Do you import custom vehicles on request?', 'a' => 'Yes, if we don’t have what you’re looking for in stock, we can source, inspect, and import specific makes and models from North America, Europe, or Asia on your behalf.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 20, 'slug' => 'purchased-car-warranty', 'category' => 'Sales', 'q' => 'What kind of warranty comes with purchased cars?', 'a' => 'Brand new vehicles come with full manufacturer warranty. Certified pre-owned cars include our standard 3-month/5,000 km warranty with optional extended coverage.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 21, 'slug' => 'vin-parts-compatibility', 'category' => 'Parts', 'q' => 'How do I know if a spare part is compatible with my car?', 'a' => 'You can provide us with your vehicle’s Chassis Number (VIN), and our parts specialists will verify 100% exact compatibility before dispatch.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 22, 'slug' => 'parts-return-policy', 'category' => 'Parts', 'q' => 'What is your return policy for auto parts?', 'a' => 'Unused, uninstalled parts in their original packaging can be returned within 14 days of purchase with a valid receipt.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 23, 'slug' => 'parts-nationwide-delivery', 'category' => 'Parts', 'q' => 'Do you deliver auto parts nationwide?', 'a' => 'Yes, we offer same-day delivery within Accra/Tema for orders placed before 12 PM, and reliable 1-2 day courier delivery nationwide.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 24, 'slug' => 'parts-installation-discount', 'category' => 'Parts', 'q' => 'Can I buy a part and have your workshop install it?', 'a' => 'Certainly. When you purchase parts from our store, our certified garage can install them for you at a discounted workshop labor rate.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 25, 'slug' => 'accepted-payment-methods', 'category' => 'Payments', 'q' => 'What payment methods do you accept?', 'a' => 'We accept Mobile Money (MTN MoMo, Telecel Cash, AT Money), major credit/debit cards (Visa, Mastercard), direct bank transfers, and cash at our branch counters.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 26, 'slug' => 'vat-invoicing-compliance', 'category' => 'Payments', 'q' => 'Can you provide VAT invoices for corporate accounting?', 'a' => 'Yes, Santa Towing is a fully incorporated Ghanaian entity and issues official GRA VAT-compliant invoices and electronic receipts.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 27, 'slug' => 'minimum-fleet-account-size', 'category' => 'Fleet', 'q' => 'What is the minimum number of cars for a corporate fleet account?', 'a' => 'Our corporate fleet maintenance and towing programs typically cater to 5+ vehicles, with custom packages available for smaller startup fleets.', 'status' => 'Published', 'hidden' => false ),
					array( 'id' => 28, 'slug' => 'fleet-partner-discounts', 'category' => 'Fleet', 'q' => 'Do corporate fleet accounts receive discounted rates?', 'a' => 'Yes, corporate partners enjoy dedicated account managers, priority 24/7 dispatch, and volume discounts on routine maintenance, tires, and towing services.', 'status' => 'Published', 'hidden' => false ),
				);

			case 'careers':
				return array(
					array(
						'id' => 1, 'slug' => 'senior-master-technician', 'title' => 'Senior Master Technician', 'department' => 'Auto Repair', 'location' => 'Accra Main Workshop', 'type' => 'Full-time', 'status' => 'Open', 'hidden' => false,
						'description' => 'We are seeking an experienced Senior Master Technician to lead complex diagnostics, engine overhauls, and mentor apprentice mechanics in our modern Accra workshop.',
						'responsibilities' => array( 'Perform advanced computerized diagnostic tests on modern vehicle electronics.', 'Lead and mentor junior technicians in mechanical repair best practices.', 'Ensure all repair work meets strict safety and factory torque standards.', 'Maintain accurate service records and communicate with service advisors.' ),
						'requirements'     => array( 'Minimum 7+ years of professional automotive repair experience.', 'Advanced diagnostic certifications (e.g. ASE Master certification or equivalent).', 'Strong leadership and verbal communication skills.', 'Proficiency with diagnostic scan tools and oscilloscope equipment.' ),
						'benefits'         => array( 'Industry-competitive base salary + productivity bonus', 'Comprehensive health insurance', 'Tool purchase allowance', 'Paid annual vacation and sick leave' ),
						'seoTitle' => 'Senior Master Technician Job in Accra | Santa Towing Careers',
						'seoDescription' => 'Join Ghana’s leading automotive team as a Senior Master Technician. Competitive compensation and state-of-the-art facilities.',
					),
					array(
						'id' => 2, 'slug' => 'heavy-duty-tow-truck-operator', 'title' => 'Heavy Duty Tow Truck Operator', 'department' => 'Towing & Recovery', 'location' => 'Kumasi Branch', 'type' => 'Full-time', 'status' => 'Open', 'hidden' => false,
						'description' => 'Join our rapid response roadside team as a Heavy Duty Tow Truck Operator handling commercial highway breakdowns, winch-outs, and accident recoveries.',
						'responsibilities' => array( 'Operate heavy-duty hydraulic towing and recovery equipment safely.', 'Provide calm, professional customer service during stressful breakdown situations.', 'Perform daily pre-trip vehicle inspections and basic truck maintenance.', 'Document all towing jobs and comply with national road traffic regulations.' ),
						'requirements'     => array( "Valid heavy-duty commercial driver's license (Class F or equivalent).", 'Clean driving record with minimum 3 years commercial driving experience.', 'Prior experience in towing or construction equipment operation.', 'Ability to work flexible shift rotas including nights and weekends.' ),
						'benefits'         => array( 'Competitive base wage + overtime allowances', 'Health insurance coverage', 'Company uniform and safety gear provided', 'Continuous defensive driving and winching safety training' ),
						'seoTitle' => 'Heavy Duty Tow Truck Operator Vacancy | Santa Towing',
						'seoDescription' => 'Santa Towing is hiring Heavy Duty Tow Truck Operators in Kumasi and Accra. Competitive pay with overtime benefits.',
					),
					array(
						'id' => 3, 'slug' => 'service-advisor', 'title' => 'Service Advisor', 'department' => 'Customer Support', 'location' => 'Accra Main Workshop', 'type' => 'Full-time', 'status' => 'Open', 'hidden' => false,
						'description' => 'Act as the primary customer liaison between vehicle owners and our workshop mechanics, translating technical vehicle needs into clear estimates.',
						'responsibilities' => array( 'Warmly greet service customers and document vehicle symptoms accurately.', 'Prepare clear repair estimates, parts pricing, and realistic timeframes.', 'Keep clients proactively updated on repair progress via WhatsApp and phone.', 'Recommend preventative maintenance services and answer customer questions.' ),
						'requirements'     => array( 'Previous experience in automotive customer service or dealership front desk.', 'Excellent verbal and written English communication skills.', 'Basic mechanical familiarity with passenger cars.', 'Proficiency with computer invoicing software.' ),
						'benefits'         => array( 'Competitive base salary + commission', 'Health insurance', 'Structured career advancement pathways' ),
						'seoTitle' => 'Service Advisor Job Vacancy Accra | Santa Towing Careers',
						'seoDescription' => 'Apply for Service Advisor position at Santa Towing. Dynamic customer service role in the automotive industry.',
					),
					array(
						'id' => 4, 'slug' => 'auto-body-painter', 'title' => 'Auto Body Painter', 'department' => 'Body Works', 'location' => 'Tema Facility', 'type' => 'Full-time', 'status' => 'Open', 'hidden' => false,
						'description' => 'Bring damaged and restored vehicles back to factory showroom shine as a precision Auto Body Painter in our dust-free spray booth.',
						'responsibilities' => array( 'Prepare surfaces, sand, mask, and mix primers and topcoats accurately.', 'Operate spray guns in our climate-controlled bake oven booth.', 'Inspect final paint finishes for flawless texture, gloss, and color match.', 'Maintain spray guns, booth filters, and personal protective equipment.' ),
						'requirements'     => array( '3+ years of professional automotive spray painting experience.', 'Knowledge of modern waterborne and solvent automotive paint systems.', 'Attention to detail, keen color vision, and steady technique.', 'Strict adherence to chemical safety protocols.' ),
						'benefits'         => array( 'Competitive monthly salary + quality performance bonuses', 'Modern oven-baked spray booth working conditions', 'Health insurance' ),
						'seoTitle' => 'Auto Body Painter Job in Tema | Santa Towing Body Works',
						'seoDescription' => 'Experienced Auto Body Painter wanted for our modern paint facility in Tema. Apply today.',
					),
				);
		}
		return array();
	}

	/**
	 * Fetch inventory items for a category from WordPress CPTs (seeding defaults automatically if empty).
	 *
	 * @param string $type Inventory type (`cars`|`rentals`|`parts`|`gallery`|`faqs`|`careers`).
	 * @return array[]
	 */
	public static function get_inventory_items( $type ) {
		$pt = self::map_inventory_cpt( $type );
		if ( ! $pt ) {
			return array();
		}

		$posts = get_posts(
			array(
				'post_type'      => $pt,
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => -1,
				'orderby'        => 'meta_value_num date',
				'meta_key'       => '_st_sort_order',
				'order'          => 'ASC',
			)
		);

		// Also catch posts without _st_sort_order set yet.
		if ( empty( $posts ) ) {
			$posts = get_posts(
				array(
					'post_type'      => $pt,
					'post_status'    => array( 'publish', 'draft' ),
					'posts_per_page' => -1,
					'orderby'        => 'date',
					'order'          => 'ASC',
				)
			);
		}

		if ( empty( $posts ) && ! get_option( 'stbm_seeded_inv_' . $type ) ) {
			self::reset_inventory_type( $type );
			$posts = get_posts(
				array(
					'post_type'      => $pt,
					'post_status'    => array( 'publish', 'draft' ),
					'posts_per_page' => -1,
					'orderby'        => 'meta_value_num ID',
					'meta_key'       => '_st_sort_order',
					'order'          => 'ASC',
				)
			);
		}

		$items = array();
		foreach ( $posts as $post ) {
			$items[] = self::hydrate_inventory_post( $type, $post );
		}
		return $items;
	}

	/**
	 * Convert a WP_Post into normalized UI dictionary matching React Inventory interfaces.
	 *
	 * @param string  $type Inventory type.
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	public static function hydrate_inventory_post( $type, $post ) {
		$pid     = (int) $post->ID;
		$hidden  = ( 'draft' === $post->post_status ) || ( '1' === get_post_meta( $pid, '_st_hidden', true ) );
		$order   = (int) get_post_meta( $pid, '_st_sort_order', true );
		$gallery = get_post_meta( $pid, '_st_gallery', true );
		$images  = array();
		if ( is_array( $gallery ) ) {
			foreach ( $gallery as $img ) {
				if ( is_array( $img ) && ! empty( $img['url'] ) ) {
					$images[] = array( 'url' => stbm_resolve_media_url( $img['url'] ), 'alt' => $img['alt'] ?? $post->post_title );
				} elseif ( is_string( $img ) && '' !== $img ) {
					$images[] = array( 'url' => stbm_resolve_media_url( $img ), 'alt' => $post->post_title );
				}
			}
		} elseif ( is_string( $gallery ) && '' !== $gallery ) {
			$dec = json_decode( $gallery, true );
			if ( is_array( $dec ) ) {
				foreach ( $dec as $img ) {
					if ( is_array( $img ) && ! empty( $img['url'] ) ) {
						$images[] = array( 'url' => stbm_resolve_media_url( $img['url'] ), 'alt' => $img['alt'] ?? $post->post_title );
					}
				}
			}
		}

		$primary_img = stbm_resolve_media_url( (string) get_post_meta( $pid, '_st_image_url', true ) );
		if ( '' === $primary_img && has_post_thumbnail( $pid ) ) {
			$primary_img = (string) get_the_post_thumbnail_url( $pid, 'large' );
		}
		if ( '' === $primary_img && ! empty( $images[0]['url'] ) ) {
			$primary_img = $images[0]['url'];
		}

		$common = array(
			'id'             => $pid,
			'legacyId'       => get_post_meta( $pid, '_st_legacy_id', true ),
			'slug'           => $post->post_name,
			'hidden'         => $hidden,
			'order'          => $order,
			'seoTitle'       => (string) get_post_meta( $pid, '_st_seo_title', true ),
			'seoDescription' => (string) get_post_meta( $pid, '_st_seo_description', true ),
		);

		switch ( $type ) {
			case 'cars':
				$specs = get_post_meta( $pid, '_st_specs', true );
				if ( is_string( $specs ) ) {
					$specs = json_decode( $specs, true );
				}
				return array_merge(
					$common,
					array(
						'name'         => $post->post_title,
						'type'         => get_post_meta( $pid, '_st_car_type', true ) ?: 'New',
						'price'        => get_post_meta( $pid, '_st_price', true ) ?: 'GH₵ 0',
						'year'         => (int) ( get_post_meta( $pid, '_st_year', true ) ?: gmdate( 'Y' ) ),
						'fuel'         => get_post_meta( $pid, '_st_fuel', true ) ?: 'Petrol',
						'transmission' => get_post_meta( $pid, '_st_transmission', true ) ?: 'Automatic',
						'mileage'      => get_post_meta( $pid, '_st_mileage', true ) ?: '0 km',
						'status'       => get_post_meta( $pid, '_st_status', true ) ?: 'Available',
						'description'  => $post->post_content,
						'image'        => $primary_img,
						'images'       => $images,
						'specs'        => is_array( $specs ) ? $specs : array( 'engine' => array(), 'performance' => array(), 'dimensions' => array(), 'features' => array() ),
					)
				);

			case 'rentals':
				$features = get_post_meta( $pid, '_st_features_json', true );
				if ( '' === $features || empty( $features ) ) {
					$features = get_post_meta( $pid, '_st_rental_features', true );
				}
				if ( is_string( $features ) ) {
					$features = json_decode( $features, true );
				}
				$cat   = get_post_meta( $pid, '_st_rental_type', true ) ?: 'Sedan';
				$daily = get_post_meta( $pid, '_st_daily_rate', true ) ?: 'GH₵ 500';
				return array_merge(
					$common,
					array(
						'name'         => $post->post_title,
						'type'         => $cat,
						'category'     => $cat,
						'categorySlug' => sanitize_title( $cat ),
						'price'        => get_post_meta( $pid, '_st_price', true ) ?: ( $daily . ' / day' ),
						'daily'        => $daily,
						'weekly'       => get_post_meta( $pid, '_st_weekly_rate', true ) ?: '',
						'monthly'      => get_post_meta( $pid, '_st_monthly_rate', true ) ?: '',
						'seats'        => (int) ( get_post_meta( $pid, '_st_seats', true ) ?: 5 ),
						'transmission' => get_post_meta( $pid, '_st_transmission', true ) ?: 'Automatic',
						'fuel'         => get_post_meta( $pid, '_st_fuel', true ) ?: 'Petrol',
						'status'       => get_post_meta( $pid, '_st_status', true ) ?: 'Available',
						'description'  => $post->post_content,
						'image'        => $primary_img,
						'images'       => $images,
						'features'     => is_array( $features ) ? $features : array(),
					)
				);

			case 'parts':
				$specs = get_post_meta( $pid, '_st_specs_json', true );
				if ( is_string( $specs ) ) {
					$specs = json_decode( $specs, true );
				}
				$status = get_post_meta( $pid, '_st_stock_status', true ) ?: 'In Stock';
				return array_merge(
					$common,
					array(
						'name'          => $post->post_title,
						'brand'         => get_post_meta( $pid, '_st_brand', true ) ?: '',
						'category'      => get_post_meta( $pid, '_st_category', true ) ?: 'Car Batteries',
						'price'         => get_post_meta( $pid, '_st_price', true ) ?: 'GH₵ 0',
						'sku'           => get_post_meta( $pid, '_st_sku', true ) ?: '',
						'rating'        => (float) ( get_post_meta( $pid, '_st_rating', true ) ?: 5.0 ),
						'reviews'       => (int) ( get_post_meta( $pid, '_st_reviews', true ) ?: 0 ),
						'inStock'       => 'Out of Stock' !== $status,
						'status'        => $status,
						'compatibility' => (string) get_post_meta( $pid, '_st_compatible_models', true ),
						'warranty'      => (string) get_post_meta( $pid, '_st_warranty', true ),
						'installation'  => (string) get_post_meta( $pid, '_st_installation', true ),
						'description'   => $post->post_content,
						'image'         => $primary_img,
						'images'        => $images,
						'specs'         => is_array( $specs ) ? $specs : array(),
					)
				);

			case 'gallery':
				$terms = wp_get_post_terms( $pid, 'st_gallery_category', array( 'fields' => 'names' ) );
				$cat   = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : ( get_post_meta( $pid, '_st_category', true ) ?: 'Workshop' );
				return array_merge(
					$common,
					array(
						'title'       => $post->post_title,
						'category'    => $cat,
						'url'         => $primary_img,
						'alt'         => get_post_meta( $pid, '_st_alt_text', true ) ?: ( get_post_meta( $pid, '_st_alt', true ) ?: $post->post_title ),
						'span'        => get_post_meta( $pid, '_st_span', true ) ?: 'col-span-1 row-span-1',
						'description' => $post->post_content,
						'status'      => get_post_meta( $pid, '_st_status', true ) ?: ( 'publish' === $post->post_status ? 'Published' : 'Draft' ),
						'images'      => $images,
					)
				);

			case 'faqs':
				$terms = wp_get_post_terms( $pid, 'st_faq_category', array( 'fields' => 'names' ) );
				$cat   = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : ( get_post_meta( $pid, '_st_category', true ) ?: 'Towing' );
				return array_merge(
					$common,
					array(
						'category' => $cat,
						'q'        => $post->post_title,
						'a'        => $post->post_content,
						'status'   => get_post_meta( $pid, '_st_status', true ) ?: ( 'publish' === $post->post_status ? 'Published' : 'Draft' ),
					)
				);

			case 'careers':
				$resp = get_post_meta( $pid, '_st_responsibilities_json', true );
				$reqs = get_post_meta( $pid, '_st_requirements_json', true );
				$bens = get_post_meta( $pid, '_st_benefits_json', true );
				$terms = wp_get_post_terms( $pid, 'st_department', array( 'fields' => 'names' ) );
				$dept  = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : ( get_post_meta( $pid, '_st_department', true ) ?: 'Towing & Recovery' );
				return array_merge(
					$common,
					array(
						'title'            => $post->post_title,
						'department'       => $dept,
						'location'         => get_post_meta( $pid, '_st_location', true ) ?: 'Accra Main Workshop',
						'type'             => get_post_meta( $pid, '_st_employment_type', true ) ?: 'Full-time',
						'description'      => $post->post_content,
						'responsibilities' => is_string( $resp ) ? ( json_decode( $resp, true ) ?: array() ) : ( is_array( $resp ) ? $resp : array() ),
						'requirements'     => is_string( $reqs ) ? ( json_decode( $reqs, true ) ?: array() ) : ( is_array( $reqs ) ? $reqs : array() ),
						'benefits'         => is_string( $bens ) ? ( json_decode( $bens, true ) ?: array() ) : ( is_array( $bens ) ? $bens : array() ),
						'status'           => get_post_meta( $pid, '_st_status', true ) ?: 'Open',
						'images'           => $images,
					)
				);
		}
		return array();
	}

	/**
	 * Create or update an inventory CPT post cleanly with all theme-compatible `_st_*` meta keys.
	 *
	 * @param string $type Inventory type.
	 * @param array  $item Item payload.
	 * @return array|WP_Error Hydrated item.
	 */
	public static function save_inventory_item( $type, $item ) {
		$pt = self::map_inventory_cpt( $type );
		if ( ! $pt ) {
			return new WP_Error( 'invalid_type', __( 'Invalid inventory type.', 'santa-towing-business-manager' ) );
		}

		$post_id = ! empty( $item['id'] ) && is_numeric( $item['id'] ) && get_post_type( (int) $item['id'] ) === $pt ? (int) $item['id'] : 0;
		$title   = sanitize_text_field( $item['name'] ?? $item['title'] ?? $item['q'] ?? __( 'Untitled Item', 'santa-towing-business-manager' ) );
		$slug    = sanitize_title( ! empty( $item['slug'] ) ? $item['slug'] : $title );
		$content = wp_kses_post( $item['description'] ?? $item['a'] ?? '' );
		$hidden  = ! empty( $item['hidden'] );

		$post_args = array(
			'post_type'    => $pt,
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
			'post_status'  => $hidden ? 'draft' : 'publish',
		);
		if ( $post_id > 0 ) {
			$post_args['ID'] = $post_id;
			$saved_id        = wp_update_post( $post_args, true );
		} else {
			$saved_id = wp_insert_post( $post_args, true );
		}

		if ( is_wp_error( $saved_id ) ) {
			return $saved_id;
		}

		$images = array();
		if ( ! empty( $item['images'] ) && is_array( $item['images'] ) ) {
			foreach ( $item['images'] as $img ) {
				if ( is_array( $img ) && ! empty( $img['url'] ) ) {
					$images[] = array(
						'url' => esc_url_raw( $img['url'] ),
						'alt' => sanitize_text_field( $img['alt'] ?? $title ),
					);
				}
			}
		}
		$primary_url = esc_url_raw( $item['image'] ?? $item['url'] ?? ( $images[0]['url'] ?? '' ) );

		update_post_meta( $saved_id, '_st_image_url', $primary_url );
		update_post_meta( $saved_id, '_st_gallery', $images );
		update_post_meta( $saved_id, '_st_hidden', $hidden ? '1' : '0' );
		update_post_meta( $saved_id, '_st_seo_title', sanitize_text_field( $item['seoTitle'] ?? '' ) );
		update_post_meta( $saved_id, '_st_seo_description', sanitize_textarea_field( $item['seoDescription'] ?? '' ) );
		if ( isset( $item['order'] ) ) {
			update_post_meta( $saved_id, '_st_sort_order', (int) $item['order'] );
		} elseif ( 0 === $post_id ) {
			update_post_meta( $saved_id, '_st_sort_order', (int) $saved_id );
		}
		if ( isset( $item['legacyId'] ) && '' !== $item['legacyId'] ) {
			update_post_meta( $saved_id, '_st_legacy_id', (int) $item['legacyId'] );
		}

		switch ( $type ) {
			case 'cars':
				$car_type = sanitize_text_field( $item['type'] ?? 'New' );
				update_post_meta( $saved_id, '_st_car_type', $car_type );
				update_post_meta( $saved_id, '_st_price', sanitize_text_field( $item['price'] ?? '' ) );
				update_post_meta( $saved_id, '_st_year', (int) ( $item['year'] ?? gmdate( 'Y' ) ) );
				update_post_meta( $saved_id, '_st_fuel', sanitize_text_field( $item['fuel'] ?? 'Petrol' ) );
				update_post_meta( $saved_id, '_st_transmission', sanitize_text_field( $item['transmission'] ?? 'Automatic' ) );
				update_post_meta( $saved_id, '_st_mileage', sanitize_text_field( $item['mileage'] ?? '0 km' ) );
				update_post_meta( $saved_id, '_st_status', sanitize_text_field( $item['status'] ?? 'Available' ) );
				update_post_meta( $saved_id, '_st_specs', is_array( $item['specs'] ?? null ) ? $item['specs'] : array() );
				if ( taxonomy_exists( 'st_car_category' ) ) {
					wp_set_object_terms( $saved_id, $car_type, 'st_car_category', false );
				}
				break;

			case 'rentals':
				$cat = sanitize_text_field( $item['category'] ?? $item['type'] ?? 'Sedan' );
				update_post_meta( $saved_id, '_st_rental_type', $cat );
				update_post_meta( $saved_id, '_st_price', sanitize_text_field( $item['price'] ?? '' ) );
				update_post_meta( $saved_id, '_st_daily_rate', sanitize_text_field( $item['daily'] ?? '' ) );
				update_post_meta( $saved_id, '_st_weekly_rate', sanitize_text_field( $item['weekly'] ?? '' ) );
				update_post_meta( $saved_id, '_st_monthly_rate', sanitize_text_field( $item['monthly'] ?? '' ) );
				update_post_meta( $saved_id, '_st_seats', (int) ( $item['seats'] ?? 5 ) );
				update_post_meta( $saved_id, '_st_transmission', sanitize_text_field( $item['transmission'] ?? 'Automatic' ) );
				update_post_meta( $saved_id, '_st_fuel', sanitize_text_field( $item['fuel'] ?? 'Petrol' ) );
				update_post_meta( $saved_id, '_st_status', sanitize_text_field( $item['status'] ?? 'Available' ) );
				update_post_meta( $saved_id, '_st_features_json', wp_json_encode( array_values( array_map( 'sanitize_text_field', (array) ( $item['features'] ?? array() ) ) ) ) );
				if ( taxonomy_exists( 'st_rental_category' ) ) {
					wp_set_object_terms( $saved_id, $cat, 'st_rental_category', false );
				}
				break;

			case 'parts':
				$cat    = sanitize_text_field( $item['category'] ?? 'Car Batteries' );
				$brand  = sanitize_text_field( $item['brand'] ?? '' );
				$status = sanitize_text_field( $item['status'] ?? 'In Stock' );
				update_post_meta( $saved_id, '_st_brand', $brand );
				update_post_meta( $saved_id, '_st_category', $cat );
				update_post_meta( $saved_id, '_st_price', sanitize_text_field( $item['price'] ?? '' ) );
				update_post_meta( $saved_id, '_st_sku', sanitize_text_field( $item['sku'] ?? '' ) );
				update_post_meta( $saved_id, '_st_rating', (float) ( $item['rating'] ?? 5.0 ) );
				update_post_meta( $saved_id, '_st_reviews', (int) ( $item['reviews'] ?? 0 ) );
				update_post_meta( $saved_id, '_st_stock_status', $status );
				update_post_meta( $saved_id, '_st_in_stock', 'Out of Stock' === $status ? '0' : '1' );
				update_post_meta( $saved_id, '_st_compatible_models', sanitize_textarea_field( $item['compatibility'] ?? '' ) );
				update_post_meta( $saved_id, '_st_warranty', sanitize_text_field( $item['warranty'] ?? '' ) );
				update_post_meta( $saved_id, '_st_installation', sanitize_textarea_field( $item['installation'] ?? '' ) );
				update_post_meta( $saved_id, '_st_specs_json', wp_json_encode( (array) ( $item['specs'] ?? array() ) ) );
				if ( taxonomy_exists( 'st_part_category' ) ) {
					wp_set_object_terms( $saved_id, $cat, 'st_part_category', false );
				}
				if ( taxonomy_exists( 'st_part_brand' ) && '' !== $brand ) {
					wp_set_object_terms( $saved_id, $brand, 'st_part_brand', false );
				}
				break;

			case 'gallery':
				$cat = sanitize_text_field( $item['category'] ?? 'Workshop' );
				update_post_meta( $saved_id, '_st_category', $cat );
				update_post_meta( $saved_id, '_st_alt_text', sanitize_text_field( $item['alt'] ?? $title ) );
				update_post_meta( $saved_id, '_st_span', sanitize_text_field( $item['span'] ?? 'col-span-1 row-span-1' ) );
				update_post_meta( $saved_id, '_st_status', sanitize_text_field( $item['status'] ?? 'Published' ) );
				if ( taxonomy_exists( 'st_gallery_category' ) ) {
					wp_set_object_terms( $saved_id, $cat, 'st_gallery_category', false );
				}
				break;

			case 'faqs':
				$cat = sanitize_text_field( $item['category'] ?? 'Towing' );
				update_post_meta( $saved_id, '_st_category', $cat );
				update_post_meta( $saved_id, '_st_status', sanitize_text_field( $item['status'] ?? 'Published' ) );
				if ( taxonomy_exists( 'st_faq_category' ) ) {
					wp_set_object_terms( $saved_id, $cat, 'st_faq_category', false );
				}
				break;

			case 'careers':
				$dept = sanitize_text_field( $item['department'] ?? 'Towing & Recovery' );
				update_post_meta( $saved_id, '_st_department', $dept );
				update_post_meta( $saved_id, '_st_location', sanitize_text_field( $item['location'] ?? 'Accra Main Workshop' ) );
				update_post_meta( $saved_id, '_st_employment_type', sanitize_text_field( $item['type'] ?? 'Full-time' ) );
				update_post_meta( $saved_id, '_st_status', sanitize_text_field( $item['status'] ?? 'Open' ) );
				update_post_meta( $saved_id, '_st_responsibilities_json', wp_json_encode( array_values( array_map( 'sanitize_text_field', (array) ( $item['responsibilities'] ?? array() ) ) ) ) );
				update_post_meta( $saved_id, '_st_requirements_json', wp_json_encode( array_values( array_map( 'sanitize_text_field', (array) ( $item['requirements'] ?? array() ) ) ) ) );
				update_post_meta( $saved_id, '_st_benefits_json', wp_json_encode( array_values( array_map( 'sanitize_text_field', (array) ( $item['benefits'] ?? array() ) ) ) ) );
				if ( taxonomy_exists( 'st_department' ) ) {
					wp_set_object_terms( $saved_id, $dept, 'st_department', false );
				}
				break;
		}

		update_option( 'stbm_seeded_inv_' . $type, 1 );
		return self::hydrate_inventory_post( $type, get_post( $saved_id ) );
	}

	/**
	 * Reset an inventory type back to factory defaults (replacing existing CPT posts of that type).
	 *
	 * @param string $type Inventory type.
	 * @return array[]
	 */
	public static function reset_inventory_type( $type ) {
		$pt = self::map_inventory_cpt( $type );
		if ( ! $pt ) {
			return array();
		}
		$existing = get_posts(
			array(
				'post_type'      => $pt,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		foreach ( $existing as $pid ) {
			wp_delete_post( $pid, true );
		}

		$defaults = self::get_default_inventory( $type );
		$saved    = array();
		foreach ( $defaults as $idx => $item ) {
			$item['legacyId'] = $item['id'] ?? ( $idx + 1 );
			unset( $item['id'] );
			$item['order'] = $idx + 1;
			$res           = self::save_inventory_item( $type, $item );
			if ( ! is_wp_error( $res ) ) {
				$saved[] = $res;
			}
		}
		update_option( 'stbm_seeded_inv_' . $type, 1 );
		return $saved;
	}
}
