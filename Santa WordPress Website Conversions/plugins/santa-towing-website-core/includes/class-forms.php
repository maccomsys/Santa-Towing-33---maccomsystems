<?php
/**
 * Secure Form Processing, Validation, File Uploads, Email Delivery & WhatsApp Dispatch
 *
 * Handles:
 * - Emergency Towing Request (`stwc_towing_request`)
 * - Haulage Quote Request (`stwc_haulage_quote`)
 * - General Contact / Enquiry (`stwc_contact_enquiry`)
 * - Corporate Fleet Consultation (`stwc_fleet_enquiry`)
 * - Car Sales / Rental / Auto Parts Inquiry (`stwc_inventory_inquiry`)
 * - Career Job Application with CV upload (`stwc_career_apply`)
 *
 * Replicates exact WhatsApp click-to-chat deep links from React source.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class STWC_Forms {

    /**
     * Singleton instance.
     *
     * @var STWC_Forms|null
     */
    private static $instance = null;

    /**
     * Return singleton instance.
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Hook AJAX handlers for logged-in & guest users.
     */
    private function __construct() {
        $actions = array(
            'stwc_towing_request'  => 'handle_towing_request',
            'stwc_haulage_quote'   => 'handle_haulage_quote',
            'stwc_contact_enquiry' => 'handle_contact_enquiry',
            'stwc_fleet_enquiry'   => 'handle_fleet_enquiry',
            'stwc_inventory_inquiry'=> 'handle_inventory_inquiry',
            'stwc_career_apply'    => 'handle_career_apply',
        );

        foreach ( $actions as $action => $method ) {
            add_action( 'wp_ajax_' . $action, array( $this, $method ) );
            add_action( 'wp_ajax_nopriv_' . $action, array( $this, $method ) );
        }
    }

    /**
     * Create custom inquiries table on plugin activation via dbDelta.
     */
    public static function create_table() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table   = $wpdb->prefix . 'santa_inquiries';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_type varchar(40) NOT NULL DEFAULT 'contact',
            customer_name varchar(150) NOT NULL DEFAULT '',
            phone varchar(60) NOT NULL DEFAULT '',
            email varchar(150) NOT NULL DEFAULT '',
            subject varchar(255) NOT NULL DEFAULT '',
            payload_json longtext NULL,
            attachment_url text NULL,
            whatsapp_url text NULL,
            status varchar(30) NOT NULL DEFAULT 'new',
            ip_hash varchar(64) NOT NULL DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY form_type (form_type),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset};";

        dbDelta( $sql );
    }

    /**
     * Shared security verification (Nonce + Honeypot).
     */
    private function verify_security() {
        if ( ! check_ajax_referer( 'stwc_form_nonce', 'nonce', false ) ) {
            wp_send_json_error(
                array( 'message' => __( 'Security session expired. Please refresh the page and try again.', 'santa-towing-website-core' ) ),
                403
            );
        }

        // Silent honeypot check to trap automated spam bots cleanly.
        $hp = isset( $_POST['website_hp'] ) ? trim( (string) wp_unslash( $_POST['website_hp'] ) ) : '';
        if ( '' !== $hp ) {
            wp_send_json_success(
                array( 'message' => __( 'Thank you. Your submission has been received.', 'santa-towing-website-core' ) )
            );
        }
    }

    /**
     * Helper to retrieve clean POST field.
     */
    private function post_text( $key, $multiline = false ) {
        if ( ! isset( $_POST[ $key ] ) ) {
            return '';
        }
        $raw = wp_unslash( (string) $_POST[ $key ] );
        return $multiline ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
    }

    /**
     * Persist inquiry into DB and send formatted admin notification email.
     */
    private function persist_and_notify( $form_type, $name, $phone, $email, $subject, $payload, $whatsapp_url = '', $attachment_url = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'santa_inquiries';

        // Ensure table exists in case plugin files were updated without re-activation hook.
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
            self::create_table();
        }

        $ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $ip_hash = $ip ? hash( 'sha256', $ip . wp_salt() ) : '';

        $wpdb->insert(
            $table,
            array(
                'form_type'      => sanitize_key( $form_type ),
                'customer_name'  => $name,
                'phone'          => $phone,
                'email'          => $email,
                'subject'        => $subject,
                'payload_json'   => wp_json_encode( $payload ),
                'attachment_url' => $attachment_url,
                'whatsapp_url'   => $whatsapp_url,
                'status'         => 'new',
                'ip_hash'        => $ip_hash,
                'created_at'     => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        $insert_id = (int) $wpdb->insert_id;

        // Deliver email via wp_mail()
        $to = sanitize_email( stwc_get_content( 'global.links.email', get_option( 'admin_email' ) ) );
        if ( ! is_email( $to ) ) {
            $to = get_option( 'admin_email' );
        }

        $lines   = array();
        $lines[] = sprintf( 'New Submission #%d (%s)', $insert_id, strtoupper( str_replace( '_', ' ', $form_type ) ) );
        $lines[] = str_repeat( '-', 44 );
        $lines[] = 'Name: ' . ( $name ?: 'N/A' );
        $lines[] = 'Phone: ' . ( $phone ?: 'N/A' );
        if ( $email ) {
            $lines[] = 'Email: ' . $email;
        }
        $lines[] = 'Subject: ' . $subject;
        $lines[] = '';
        foreach ( $payload as $k => $v ) {
            $label   = ucwords( str_replace( '_', ' ', (string) $k ) );
            $lines[] = $label . ': ' . ( is_scalar( $v ) ? (string) $v : wp_json_encode( $v ) );
        }
        if ( $attachment_url ) {
            $lines[] = '';
            $lines[] = 'Uploaded File: ' . $attachment_url;
        }
        if ( $whatsapp_url ) {
            $lines[] = '';
            $lines[] = 'WhatsApp Reply Link: ' . $whatsapp_url;
        }

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
        if ( is_email( $email ) ) {
            $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
        }

        wp_mail(
            $to,
            '[Santa Towing] ' . $subject,
            implode( "\n", $lines ),
            $headers
        );

        return $insert_id;
    }

    /**
     * 1. Handle Emergency Towing Request (`src/pages/Towing.tsx` parity).
     */
    public function handle_towing_request() {
        $this->verify_security();

        $name   = $this->post_text( 'name' );
        $phone  = $this->post_text( 'phone' );
        $region = $this->post_text( 'region' );
        $area   = $this->post_text( 'area' );
        $car    = $this->post_text( 'carMakeModel' );
        $issue  = $this->post_text( 'issue' );

        if ( '' === $name || '' === $phone || '' === $region || '' === $area ) {
            wp_send_json_error(
                array( 'message' => __( 'Please complete your name, phone number, Ghana region, and area/town.', 'santa-towing-website-core' ) ),
                400
            );
        }

        $issue_labels = array(
            'breakdown' => 'Car Breakdown',
            'accident'  => 'Accident Recovery',
            'flat-tire' => 'Flat Tire',
            'fuel'      => 'Out of Fuel',
            'lockout'   => 'Car Lockout',
            'other'     => 'Other Roadside Issue',
        );
        $issue_text = $issue_labels[ $issue ] ?? ( $issue ?: 'Emergency Towing Required' );

        // Exact WhatsApp dispatch message from React Towing.tsx
        $wa_message = "🚨 *EMERGENCY TOWING REQUEST - SANTA TOWING*\n\n"
            . "👤 *Name:* {$name}\n"
            . "📞 *Phone Number:* {$phone}\n"
            . "📍 *Region:* {$region}\n"
            . "📌 *Area / Town:* {$area}\n"
            . "🚗 *Car Make & Model:* " . ( $car ?: 'Not provided' ) . "\n"
            . "⚠️ *Issue / Situation:* {$issue_text}\n\n"
            . '_Please dispatch an emergency tow truck to my location as soon as possible._';

        $whatsapp_url = stwc_build_whatsapp_url( $wa_message );
        $subject      = "Emergency Towing Request: {$area}, {$region}";

        $id = $this->persist_and_notify(
            'towing',
            $name,
            $phone,
            '',
            $subject,
            array(
                'region'         => $region,
                'area_town'      => $area,
                'car_make_model' => $car,
                'issue'          => $issue_text,
            ),
            $whatsapp_url
        );

        wp_send_json_success(
            array(
                'inquiry_id'   => $id,
                'whatsapp_url' => $whatsapp_url,
                'message'      => __( 'Towing dispatch alert logged! Opening WhatsApp to connect you immediately with our nearest driver.', 'santa-towing-website-core' ),
            )
        );
    }

    /**
     * 2. Handle Haulage Quote Request (`src/pages/Haulage.tsx` parity).
     */
    public function handle_haulage_quote() {
        $this->verify_security();

        $name     = $this->post_text( 'customerName' );
        $phone    = $this->post_text( 'phoneNumber' );
        $pickup   = $this->post_text( 'pickupLocation' );
        $delivery = $this->post_text( 'deliveryLocation' );
        $cargo    = $this->post_text( 'cargoType' ) ?: 'Heavy Machinery Transport';
        $weight   = $this->post_text( 'estimatedWeight' );
        $desc     = $this->post_text( 'cargoDescription', true );
        $date     = $this->post_text( 'preferredDate' );
        $notes    = $this->post_text( 'additionalInfo', true );

        if ( '' === $name || '' === $phone || '' === $pickup || '' === $delivery ) {
            wp_send_json_error(
                array( 'message' => __( 'Please fill in your name, phone number, pickup location, and delivery destination.', 'santa-towing-website-core' ) ),
                400
            );
        }

        // Exact WhatsApp message format from React Haulage.tsx
        $wa_message = "*SANTA TOWING - HAULAGE QUOTE REQUEST*\n"
            . "----------------------------------------\n"
            . '*Name:* ' . $name . "\n"
            . '*Phone:* ' . $phone . "\n"
            . '*Pickup:* ' . $pickup . "\n"
            . '*Delivery:* ' . $delivery . "\n"
            . '*Cargo Type:* ' . $cargo . "\n"
            . '*Cargo Description:* ' . ( $desc ?: 'N/A' ) . "\n"
            . '*Estimated Weight:* ' . ( $weight ?: 'Not specified' ) . "\n"
            . '*Preferred Date:* ' . ( $date ?: 'Earliest available' ) . "\n"
            . '*Additional Info:* ' . ( $notes ?: 'None' ) . "\n"
            . "----------------------------------------\n"
            . 'Please provide a haulage rate estimate.';

        $whatsapp_url = stwc_build_whatsapp_url( $wa_message );
        $subject      = "Haulage Quote: {$cargo} ({$pickup} → {$delivery})";

        $id = $this->persist_and_notify(
            'haulage',
            $name,
            $phone,
            '',
            $subject,
            array(
                'pickup_location'   => $pickup,
                'delivery_location' => $delivery,
                'cargo_type'        => $cargo,
                'estimated_weight'  => $weight,
                'cargo_description' => $desc,
                'preferred_date'    => $date,
                'additional_info'   => $notes,
            ),
            $whatsapp_url
        );

        wp_send_json_success(
            array(
                'inquiry_id'   => $id,
                'whatsapp_url' => $whatsapp_url,
                'message'      => __( 'Haulage Quote Request Received! Click below to send via WhatsApp for immediate confirmation.', 'santa-towing-website-core' ),
            )
        );
    }

    /**
     * 3. Handle General Contact & Garage Service Enquiry.
     */
    public function handle_contact_enquiry() {
        $this->verify_security();

        $name    = $this->post_text( 'name' );
        $phone   = $this->post_text( 'phone' );
        $email   = sanitize_email( $this->post_text( 'email' ) );
        $service = $this->post_text( 'serviceType' ) ?: 'General Enquiry';
        $message = $this->post_text( 'message', true );

        if ( '' === $name || ( '' === $phone && '' === $email ) ) {
            wp_send_json_error(
                array( 'message' => __( 'Please provide your name and at least a phone number or email address.', 'santa-towing-website-core' ) ),
                400
            );
        }

        $wa_message = "*SANTA TOWING - GENERAL ENQUIRY*\n"
            . "*Name:* {$name}\n"
            . "*Phone:* " . ( $phone ?: 'N/A' ) . "\n"
            . ( $email ? "*Email:* {$email}\n" : '' )
            . "*Topic:* {$service}\n"
            . '*Message:* ' . ( $message ?: 'Requesting assistance' );

        $whatsapp_url = stwc_build_whatsapp_url( $wa_message );
        $id           = $this->persist_and_notify(
            'contact',
            $name,
            $phone,
            $email,
            "Contact Enquiry: {$service}",
            array(
                'service_topic' => $service,
                'message'       => $message,
            ),
            $whatsapp_url
        );

        wp_send_json_success(
            array(
                'inquiry_id'   => $id,
                'whatsapp_url' => $whatsapp_url,
                'message'      => __( 'Thank you! Our team has received your enquiry and will respond shortly.', 'santa-towing-website-core' ),
            )
        );
    }

    /**
     * 4. Handle Corporate Fleet Consultation Enquiry.
     */
    public function handle_fleet_enquiry() {
        $this->verify_security();

        $company    = $this->post_text( 'companyName' );
        $name       = $this->post_text( 'contactName' );
        $phone      = $this->post_text( 'phone' );
        $email      = sanitize_email( $this->post_text( 'email' ) );
        $fleet_size = $this->post_text( 'fleetSize' );
        $notes      = $this->post_text( 'notes', true );

        if ( '' === $name || '' === $phone ) {
            wp_send_json_error(
                array( 'message' => __( 'Please enter your contact name and phone number.', 'santa-towing-website-core' ) ),
                400
            );
        }

        $wa_message = "*SANTA TOWING - CORPORATE FLEET CONSULTATION*\n"
            . '*Company:* ' . ( $company ?: 'Corporate Client' ) . "\n"
            . "*Contact Person:* {$name}\n"
            . "*Phone:* {$phone}\n"
            . '*Fleet Size:* ' . ( $fleet_size ?: '5+ vehicles' ) . "\n"
            . '*Requirements:* ' . ( $notes ?: 'Fleet maintenance & VIP towing SLA' );

        $whatsapp_url = stwc_build_whatsapp_url( $wa_message );
        $id           = $this->persist_and_notify(
            'fleet',
            $name,
            $phone,
            $email,
            'Corporate Fleet Consultation: ' . ( $company ?: $name ),
            array(
                'company'    => $company,
                'fleet_size' => $fleet_size,
                'notes'      => $notes,
            ),
            $whatsapp_url
        );

        wp_send_json_success(
            array(
                'inquiry_id'   => $id,
                'whatsapp_url' => $whatsapp_url,
                'message'      => __( 'Corporate Fleet request submitted! A dedicated account manager will contact you within 2 business hours.', 'santa-towing-website-core' ),
            )
        );
    }

    /**
     * 5. Handle Car Sales, Rental Booking & Auto Part Availability Inquiries.
     */
    public function handle_inventory_inquiry() {
        $this->verify_security();

        $kind        = $this->post_text( 'inquiryKind' ) ?: 'car_inquiry';
        $allowed     = array( 'car_inquiry', 'rental_booking', 'part_inquiry' );
        if ( ! in_array( $kind, $allowed, true ) ) {
            $kind = 'car_inquiry';
        }

        $item_title  = $this->post_text( 'itemTitle' );
        $item_id     = absint( $_POST['itemId'] ?? 0 );
        $name        = $this->post_text( 'name' );
        $phone       = $this->post_text( 'phone' );
        $email       = sanitize_email( $this->post_text( 'email' ) );
        $pickup_date = $this->post_text( 'pickupDate' );
        $duration    = $this->post_text( 'duration' );
        $with_driver = $this->post_text( 'withDriver' );
        $vin         = $this->post_text( 'vinNumber' );
        $notes       = $this->post_text( 'notes', true );

        if ( '' === $name || '' === $phone ) {
            wp_send_json_error(
                array( 'message' => __( 'Please enter your full name and phone number.', 'santa-towing-website-core' ) ),
                400
            );
        }

        $header_map = array(
            'car_inquiry'    => '*SANTA TOWING - VEHICLE SALES INQUIRY*',
            'rental_booking' => '*SANTA TOWING - CAR RENTAL RESERVATION*',
            'part_inquiry'   => '*SANTA TOWING - AUTO PART AVAILABILITY INQUIRY*',
        );

        $wa_lines = array(
            $header_map[ $kind ],
            '*Item:* ' . ( $item_title ?: 'Vehicle / Part #' . $item_id ),
            '*Name:* ' . $name,
            '*Phone:* ' . $phone,
        );
        if ( 'rental_booking' === $kind ) {
            $wa_lines[] = '*Pickup Date:* ' . ( $pickup_date ?: 'Flexible' );
            $wa_lines[] = '*Duration:* ' . ( $duration ?: 'Daily' );
            $wa_lines[] = '*Chauffeur:* ' . ( 'yes' === $with_driver ? 'With Driver' : 'Self-Drive' );
        }
        if ( 'part_inquiry' === $kind && $vin ) {
            $wa_lines[] = '*Vehicle VIN / Chassis:* ' . $vin;
        }
        if ( $notes ) {
            $wa_lines[] = '*Notes:* ' . $notes;
        }

        $whatsapp_url = stwc_build_whatsapp_url( implode( "\n", $wa_lines ) );
        $subject      = ucwords( str_replace( '_', ' ', $kind ) ) . ': ' . ( $item_title ?: '#' . $item_id );

        $id = $this->persist_and_notify(
            $kind,
            $name,
            $phone,
            $email,
            $subject,
            array(
                'item_id'     => $item_id,
                'item_title'  => $item_title,
                'pickup_date' => $pickup_date,
                'duration'    => $duration,
                'with_driver' => $with_driver,
                'vin'         => $vin,
                'notes'       => $notes,
            ),
            $whatsapp_url
        );

        wp_send_json_success(
            array(
                'inquiry_id'   => $id,
                'whatsapp_url' => $whatsapp_url,
                'message'      => __( 'Request submitted! Our sales desk has been notified and you can also chat directly on WhatsApp below.', 'santa-towing-website-core' ),
            )
        );
    }

    /**
     * 6. Handle Career Application + Secure CV Upload (`src/pages/Careers.tsx` parity).
     */
    public function handle_career_apply() {
        $this->verify_security();

        $job_title = $this->post_text( 'jobTitle' ) ?: 'Open Role Application';
        $name      = $this->post_text( 'fullName' );
        $email     = sanitize_email( $this->post_text( 'email' ) );
        $phone     = $this->post_text( 'phone' );
        $portfolio = esc_url_raw( $this->post_text( 'portfolioUrl' ) );
        $cover     = $this->post_text( 'coverLetter', true );

        if ( '' === $name || ! is_email( $email ) || '' === $phone ) {
            wp_send_json_error(
                array( 'message' => __( 'Please fill in your full name, valid email address, and phone number.', 'santa-towing-website-core' ) ),
                400
            );
        }

        $attachment_url = '';
        if ( isset( $_FILES['resumeFile'] ) && ! empty( $_FILES['resumeFile']['name'] ) ) {
            $file = $_FILES['resumeFile'];
            if ( ! empty( $file['error'] ) && UPLOAD_ERR_OK !== (int) $file['error'] ) {
                wp_send_json_error( array( 'message' => __( 'File upload failed. Please try again.', 'santa-towing-website-core' ) ), 400 );
            }
            if ( (int) $file['size'] > 5 * 1024 * 1024 ) {
                wp_send_json_error( array( 'message' => __( 'Resume/CV file must be 5MB or smaller.', 'santa-towing-website-core' ) ), 400 );
            }

            $allowed_mimes = array(
                'pdf'  => 'application/pdf',
                'doc'  => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            );

            $check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed_mimes );
            if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
                wp_send_json_error( array( 'message' => __( 'Only PDF, DOC, or DOCX resume files are accepted.', 'santa-towing-website-core' ) ), 400 );
            }

            require_once ABSPATH . 'wp-admin/includes/file.php';
            $upload = wp_handle_upload(
                $file,
                array(
                    'test_form' => false,
                    'mimes'     => $allowed_mimes,
                )
            );

            if ( isset( $upload['error'] ) ) {
                wp_send_json_error( array( 'message' => sanitize_text_field( $upload['error'] ) ), 400 );
            }
            $attachment_url = esc_url_raw( $upload['url'] ?? '' );
        }

        $id = $this->persist_and_notify(
            'career',
            $name,
            $phone,
            $email,
            "Job Application: {$job_title} ({$name})",
            array(
                'job_title'    => $job_title,
                'portfolio'    => $portfolio,
                'cover_letter' => $cover,
            ),
            '',
            $attachment_url
        );

        wp_send_json_success(
            array(
                'inquiry_id' => $id,
                'message'    => __( 'Application Submitted! Thank you for applying to Santa Towing. Our HR team will review your CV and contact shortlisted candidates.', 'santa-towing-website-core' ),
            )
        );
    }
}
