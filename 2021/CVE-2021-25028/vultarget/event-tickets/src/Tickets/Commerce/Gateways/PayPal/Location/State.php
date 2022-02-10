<?php

/**
 * Class State.
 *
 * Currently this file is not in use for PayPal yet, but we might implement in the near future, this is code ported from
 * GiveWP that we modified, but it's not in use. Modifications and usage should be verified.
 *
 * @since 5.2.0
 */
class State {
	/**
	 * Get Turkey States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_turkey_list() {
		$states = [
			''     => '',
			'TR01' => __( 'Adana', 'event-tickets' ),
			'TR02' => __( 'Ad&#305;yaman', 'event-tickets' ),
			'TR03' => __( 'Afyon', 'event-tickets' ),
			'TR04' => __( 'A&#287;r&#305;', 'event-tickets' ),
			'TR05' => __( 'Amasya', 'event-tickets' ),
			'TR06' => __( 'Ankara', 'event-tickets' ),
			'TR07' => __( 'Antalya', 'event-tickets' ),
			'TR08' => __( 'Artvin', 'event-tickets' ),
			'TR09' => __( 'Ayd&#305;n', 'event-tickets' ),
			'TR10' => __( 'Bal&#305;kesir', 'event-tickets' ),
			'TR11' => __( 'Bilecik', 'event-tickets' ),
			'TR12' => __( 'Bing&#246;l', 'event-tickets' ),
			'TR13' => __( 'Bitlis', 'event-tickets' ),
			'TR14' => __( 'Bolu', 'event-tickets' ),
			'TR15' => __( 'Burdur', 'event-tickets' ),
			'TR16' => __( 'Bursa', 'event-tickets' ),
			'TR17' => __( '&#199;anakkale', 'event-tickets' ),
			'TR18' => __( '&#199;ank&#305;r&#305;', 'event-tickets' ),
			'TR19' => __( '&#199;orum', 'event-tickets' ),
			'TR20' => __( 'Denizli', 'event-tickets' ),
			'TR21' => __( 'Diyarbak&#305;r', 'event-tickets' ),
			'TR22' => __( 'Edirne', 'event-tickets' ),
			'TR23' => __( 'Elaz&#305;&#287;', 'event-tickets' ),
			'TR24' => __( 'Erzincan', 'event-tickets' ),
			'TR25' => __( 'Erzurum', 'event-tickets' ),
			'TR26' => __( 'Eski&#351;ehir', 'event-tickets' ),
			'TR27' => __( 'Gaziantep', 'event-tickets' ),
			'TR28' => __( 'Giresun', 'event-tickets' ),
			'TR29' => __( 'G&#252;m&#252;&#351;hane', 'event-tickets' ),
			'TR30' => __( 'Hakkari', 'event-tickets' ),
			'TR31' => __( 'Hatay', 'event-tickets' ),
			'TR32' => __( 'Isparta', 'event-tickets' ),
			'TR33' => __( '&#304;&#231;el', 'event-tickets' ),
			'TR34' => __( '&#304;stanbul', 'event-tickets' ),
			'TR35' => __( '&#304;zmir', 'event-tickets' ),
			'TR36' => __( 'Kars', 'event-tickets' ),
			'TR37' => __( 'Kastamonu', 'event-tickets' ),
			'TR38' => __( 'Kayseri', 'event-tickets' ),
			'TR39' => __( 'K&#305;rklareli', 'event-tickets' ),
			'TR40' => __( 'K&#305;r&#351;ehir', 'event-tickets' ),
			'TR41' => __( 'Kocaeli', 'event-tickets' ),
			'TR42' => __( 'Konya', 'event-tickets' ),
			'TR43' => __( 'K&#252;tahya', 'event-tickets' ),
			'TR44' => __( 'Malatya', 'event-tickets' ),
			'TR45' => __( 'Manisa', 'event-tickets' ),
			'TR46' => __( 'Kahramanmara&#351;', 'event-tickets' ),
			'TR47' => __( 'Mardin', 'event-tickets' ),
			'TR48' => __( 'Mu&#287;la', 'event-tickets' ),
			'TR49' => __( 'Mu&#351;', 'event-tickets' ),
			'TR50' => __( 'Nev&#351;ehir', 'event-tickets' ),
			'TR51' => __( 'Ni&#287;de', 'event-tickets' ),
			'TR52' => __( 'Ordu', 'event-tickets' ),
			'TR53' => __( 'Rize', 'event-tickets' ),
			'TR54' => __( 'Sakarya', 'event-tickets' ),
			'TR55' => __( 'Samsun', 'event-tickets' ),
			'TR56' => __( 'Siirt', 'event-tickets' ),
			'TR57' => __( 'Sinop', 'event-tickets' ),
			'TR58' => __( 'Sivas', 'event-tickets' ),
			'TR59' => __( 'Tekirda&#287;', 'event-tickets' ),
			'TR60' => __( 'Tokat', 'event-tickets' ),
			'TR61' => __( 'Trabzon', 'event-tickets' ),
			'TR62' => __( 'Tunceli', 'event-tickets' ),
			'TR63' => __( '&#350;anl&#305;urfa', 'event-tickets' ),
			'TR64' => __( 'U&#351;ak', 'event-tickets' ),
			'TR65' => __( 'Van', 'event-tickets' ),
			'TR66' => __( 'Yozgat', 'event-tickets' ),
			'TR67' => __( 'Zonguldak', 'event-tickets' ),
			'TR68' => __( 'Aksaray', 'event-tickets' ),
			'TR69' => __( 'Bayburt', 'event-tickets' ),
			'TR70' => __( 'Karaman', 'event-tickets' ),
			'TR71' => __( 'K&#305;r&#305;kkale', 'event-tickets' ),
			'TR72' => __( 'Batman', 'event-tickets' ),
			'TR73' => __( '&#350;&#305;rnak', 'event-tickets' ),
			'TR74' => __( 'Bart&#305;n', 'event-tickets' ),
			'TR75' => __( 'Ardahan', 'event-tickets' ),
			'TR76' => __( 'I&#287;d&#305;r', 'event-tickets' ),
			'TR77' => __( 'Yalova', 'event-tickets' ),
			'TR78' => __( 'Karab&#252;k', 'event-tickets' ),
			'TR79' => __( 'Kilis', 'event-tickets' ),
			'TR80' => __( 'Osmaniye', 'event-tickets' ),
			'TR81' => __( 'D&#252;zce', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_turkey_states', $states );
	}

	/**
	 * Get Romania States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_romania_list() {
		$states = [
			''   => '',
			'AB' => __( 'Alba', 'event-tickets' ),
			'AR' => __( 'Arad', 'event-tickets' ),
			'AG' => __( 'Arges', 'event-tickets' ),
			'BC' => __( 'Bacau', 'event-tickets' ),
			'BH' => __( 'Bihor', 'event-tickets' ),
			'BN' => __( 'Bistrita-Nasaud', 'event-tickets' ),
			'BT' => __( 'Botosani', 'event-tickets' ),
			'BR' => __( 'Braila', 'event-tickets' ),
			'BV' => __( 'Brasov', 'event-tickets' ),
			'B'  => __( 'Bucuresti', 'event-tickets' ),
			'BZ' => __( 'Buzau', 'event-tickets' ),
			'CL' => __( 'Calarasi', 'event-tickets' ),
			'CS' => __( 'Caras-Severin', 'event-tickets' ),
			'CJ' => __( 'Cluj', 'event-tickets' ),
			'CT' => __( 'Constanta', 'event-tickets' ),
			'CV' => __( 'Covasna', 'event-tickets' ),
			'DB' => __( 'Dambovita', 'event-tickets' ),
			'DJ' => __( 'Dolj', 'event-tickets' ),
			'GL' => __( 'Galati', 'event-tickets' ),
			'GR' => __( 'Giurgiu', 'event-tickets' ),
			'GJ' => __( 'Gorj', 'event-tickets' ),
			'HR' => __( 'Harghita', 'event-tickets' ),
			'HD' => __( 'Hunedoara', 'event-tickets' ),
			'IL' => __( 'Ialomita', 'event-tickets' ),
			'IS' => __( 'Iasi', 'event-tickets' ),
			'IF' => __( 'Ilfov', 'event-tickets' ),
			'MM' => __( 'Maramures', 'event-tickets' ),
			'MH' => __( 'Mehedinti', 'event-tickets' ),
			'MS' => __( 'Mures', 'event-tickets' ),
			'NT' => __( 'Neamt', 'event-tickets' ),
			'OT' => __( 'Olt', 'event-tickets' ),
			'PH' => __( 'Prahova', 'event-tickets' ),
			'SJ' => __( 'Salaj', 'event-tickets' ),
			'SM' => __( 'Satu Mare', 'event-tickets' ),
			'SB' => __( 'Sibiu', 'event-tickets' ),
			'SV' => __( 'Suceava', 'event-tickets' ),
			'TR' => __( 'Teleorman', 'event-tickets' ),
			'TM' => __( 'Timis', 'event-tickets' ),
			'TL' => __( 'Tulcea', 'event-tickets' ),
			'VL' => __( 'Valcea', 'event-tickets' ),
			'VS' => __( 'Vaslui', 'event-tickets' ),
			'VN' => __( 'Vrancea', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_romania_states', $states );
	}

	/**
	 * Get Pakistan States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_pakistan_list() {
		$states = [
			''   => '',
			'JK' => __( 'Azad Kashmir', 'event-tickets' ),
			'BA' => __( 'Balochistan', 'event-tickets' ),
			'TA' => __( 'FATA', 'event-tickets' ),
			'GB' => __( 'Gilgit Baltistan', 'event-tickets' ),
			'IS' => __( 'Islamabad Capital Territory', 'event-tickets' ),
			'KP' => __( 'Khyber Pakhtunkhwa', 'event-tickets' ),
			'PB' => __( 'Punjab', 'event-tickets' ),
			'SD' => __( 'Sindh', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_pakistan_states', $states );
	}

	/**
	 * Get Philippines States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_philippines_list() {
		$states = [
			''    => '',
			'ABR' => __( 'Abra', 'event-tickets' ),
			'AGN' => __( 'Agusan del Norte', 'event-tickets' ),
			'AGS' => __( 'Agusan del Sur', 'event-tickets' ),
			'AKL' => __( 'Aklan', 'event-tickets' ),
			'ALB' => __( 'Albay', 'event-tickets' ),
			'ANT' => __( 'Antique', 'event-tickets' ),
			'APA' => __( 'Apayao', 'event-tickets' ),
			'AUR' => __( 'Aurora', 'event-tickets' ),
			'BAS' => __( 'Basilan', 'event-tickets' ),
			'BAN' => __( 'Bataan', 'event-tickets' ),
			'BTN' => __( 'Batanes', 'event-tickets' ),
			'BTG' => __( 'Batangas', 'event-tickets' ),
			'BEN' => __( 'Benguet', 'event-tickets' ),
			'BIL' => __( 'Biliran', 'event-tickets' ),
			'BOH' => __( 'Bohol', 'event-tickets' ),
			'BUK' => __( 'Bukidnon', 'event-tickets' ),
			'BUL' => __( 'Bulacan', 'event-tickets' ),
			'CAG' => __( 'Cagayan', 'event-tickets' ),
			'CAN' => __( 'Camarines Norte', 'event-tickets' ),
			'CAS' => __( 'Camarines Sur', 'event-tickets' ),
			'CAM' => __( 'Camiguin', 'event-tickets' ),
			'CAP' => __( 'Capiz', 'event-tickets' ),
			'CAT' => __( 'Catanduanes', 'event-tickets' ),
			'CAV' => __( 'Cavite', 'event-tickets' ),
			'CEB' => __( 'Cebu', 'event-tickets' ),
			'COM' => __( 'Compostela Valley', 'event-tickets' ),
			'NCO' => __( 'Cotabato', 'event-tickets' ),
			'DAV' => __( 'Davao del Norte', 'event-tickets' ),
			'DAS' => __( 'Davao del Sur', 'event-tickets' ),
			'DAC' => __( 'Davao Occidental', 'event-tickets' ), // TODO: Needs to be updated when ISO code is assigned
			'DAO' => __( 'Davao Oriental', 'event-tickets' ),
			'DIN' => __( 'Dinagat Islands', 'event-tickets' ),
			'EAS' => __( 'Eastern Samar', 'event-tickets' ),
			'GUI' => __( 'Guimaras', 'event-tickets' ),
			'IFU' => __( 'Ifugao', 'event-tickets' ),
			'ILN' => __( 'Ilocos Norte', 'event-tickets' ),
			'ILS' => __( 'Ilocos Sur', 'event-tickets' ),
			'ILI' => __( 'Iloilo', 'event-tickets' ),
			'ISA' => __( 'Isabela', 'event-tickets' ),
			'KAL' => __( 'Kalinga', 'event-tickets' ),
			'LUN' => __( 'La Union', 'event-tickets' ),
			'LAG' => __( 'Laguna', 'event-tickets' ),
			'LAN' => __( 'Lanao del Norte', 'event-tickets' ),
			'LAS' => __( 'Lanao del Sur', 'event-tickets' ),
			'LEY' => __( 'Leyte', 'event-tickets' ),
			'MAG' => __( 'Maguindanao', 'event-tickets' ),
			'MAD' => __( 'Marinduque', 'event-tickets' ),
			'MAS' => __( 'Masbate', 'event-tickets' ),
			'MSC' => __( 'Misamis Occidental', 'event-tickets' ),
			'MSR' => __( 'Misamis Oriental', 'event-tickets' ),
			'MOU' => __( 'Mountain Province', 'event-tickets' ),
			'NEC' => __( 'Negros Occidental', 'event-tickets' ),
			'NER' => __( 'Negros Oriental', 'event-tickets' ),
			'NSA' => __( 'Northern Samar', 'event-tickets' ),
			'NUE' => __( 'Nueva Ecija', 'event-tickets' ),
			'NUV' => __( 'Nueva Vizcaya', 'event-tickets' ),
			'MDC' => __( 'Occidental Mindoro', 'event-tickets' ),
			'MDR' => __( 'Oriental Mindoro', 'event-tickets' ),
			'PLW' => __( 'Palawan', 'event-tickets' ),
			'PAM' => __( 'Pampanga', 'event-tickets' ),
			'PAN' => __( 'Pangasinan', 'event-tickets' ),
			'QUE' => __( 'Quezon', 'event-tickets' ),
			'QUI' => __( 'Quirino', 'event-tickets' ),
			'RIZ' => __( 'Rizal', 'event-tickets' ),
			'ROM' => __( 'Romblon', 'event-tickets' ),
			'WSA' => __( 'Samar', 'event-tickets' ),
			'SAR' => __( 'Sarangani', 'event-tickets' ),
			'SIQ' => __( 'Siquijor', 'event-tickets' ),
			'SOR' => __( 'Sorsogon', 'event-tickets' ),
			'SCO' => __( 'South Cotabato', 'event-tickets' ),
			'SLE' => __( 'Southern Leyte', 'event-tickets' ),
			'SUK' => __( 'Sultan Kudarat', 'event-tickets' ),
			'SLU' => __( 'Sulu', 'event-tickets' ),
			'SUN' => __( 'Surigao del Norte', 'event-tickets' ),
			'SUR' => __( 'Surigao del Sur', 'event-tickets' ),
			'TAR' => __( 'Tarlac', 'event-tickets' ),
			'TAW' => __( 'Tawi-Tawi', 'event-tickets' ),
			'ZMB' => __( 'Zambales', 'event-tickets' ),
			'ZAN' => __( 'Zamboanga del Norte', 'event-tickets' ),
			'ZAS' => __( 'Zamboanga del Sur', 'event-tickets' ),
			'ZSI' => __( 'Zamboanga Sibugay', 'event-tickets' ),
			'00'  => __( 'Metro Manila', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_philippines_states', $states );
	}

	/**
	 * Get Peru States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_peru_list() {
		$states = [
			''    => '',
			'CAL' => __( 'El Callao', 'event-tickets' ),
			'LMA' => __( 'Municipalidad Metropolitana de Lima', 'event-tickets' ),
			'AMA' => __( 'Amazonas', 'event-tickets' ),
			'ANC' => __( 'Ancash', 'event-tickets' ),
			'APU' => __( 'Apur&iacute;mac', 'event-tickets' ),
			'ARE' => __( 'Arequipa', 'event-tickets' ),
			'AYA' => __( 'Ayacucho', 'event-tickets' ),
			'CAJ' => __( 'Cajamarca', 'event-tickets' ),
			'CUS' => __( 'Cusco', 'event-tickets' ),
			'HUV' => __( 'Huancavelica', 'event-tickets' ),
			'HUC' => __( 'Hu&aacute;nuco', 'event-tickets' ),
			'ICA' => __( 'Ica', 'event-tickets' ),
			'JUN' => __( 'Jun&iacute;n', 'event-tickets' ),
			'LAL' => __( 'La Libertad', 'event-tickets' ),
			'LAM' => __( 'Lambayeque', 'event-tickets' ),
			'LIM' => __( 'Lima', 'event-tickets' ),
			'LOR' => __( 'Loreto', 'event-tickets' ),
			'MDD' => __( 'Madre de Dios', 'event-tickets' ),
			'MOQ' => __( 'Moquegua', 'event-tickets' ),
			'PAS' => __( 'Pasco', 'event-tickets' ),
			'PIU' => __( 'Piura', 'event-tickets' ),
			'PUN' => __( 'Puno', 'event-tickets' ),
			'SAM' => __( 'San Mart&iacute;n', 'event-tickets' ),
			'TAC' => __( 'Tacna', 'event-tickets' ),
			'TUM' => __( 'Tumbes', 'event-tickets' ),
			'UCA' => __( 'Ucayali', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_peru_states', $states );
	}

	/**
	 * Get Nepal States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_nepal_list() {
		$states = [
			''    => '',
			'BAG' => __( 'Bagmati', 'event-tickets' ),
			'BHE' => __( 'Bheri', 'event-tickets' ),
			'DHA' => __( 'Dhaulagiri', 'event-tickets' ),
			'GAN' => __( 'Gandaki', 'event-tickets' ),
			'JAN' => __( 'Janakpur', 'event-tickets' ),
			'KAR' => __( 'Karnali', 'event-tickets' ),
			'KOS' => __( 'Koshi', 'event-tickets' ),
			'LUM' => __( 'Lumbini', 'event-tickets' ),
			'MAH' => __( 'Mahakali', 'event-tickets' ),
			'MEC' => __( 'Mechi', 'event-tickets' ),
			'NAR' => __( 'Narayani', 'event-tickets' ),
			'RAP' => __( 'Rapti', 'event-tickets' ),
			'SAG' => __( 'Sagarmatha', 'event-tickets' ),
			'SET' => __( 'Seti', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_nepal_states', $states );
	}

	/**
	 * Get Nigerian States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_nigerian_list() {
		$states = [
			''   => '',
			'AB' => __( 'Abia', 'event-tickets' ),
			'FC' => __( 'Abuja', 'event-tickets' ),
			'AD' => __( 'Adamawa', 'event-tickets' ),
			'AK' => __( 'Akwa Ibom', 'event-tickets' ),
			'AN' => __( 'Anambra', 'event-tickets' ),
			'BA' => __( 'Bauchi', 'event-tickets' ),
			'BY' => __( 'Bayelsa', 'event-tickets' ),
			'BE' => __( 'Benue', 'event-tickets' ),
			'BO' => __( 'Borno', 'event-tickets' ),
			'CR' => __( 'Cross River', 'event-tickets' ),
			'DE' => __( 'Delta', 'event-tickets' ),
			'EB' => __( 'Ebonyi', 'event-tickets' ),
			'ED' => __( 'Edo', 'event-tickets' ),
			'EK' => __( 'Ekiti', 'event-tickets' ),
			'EN' => __( 'Enugu', 'event-tickets' ),
			'GO' => __( 'Gombe', 'event-tickets' ),
			'IM' => __( 'Imo', 'event-tickets' ),
			'JI' => __( 'Jigawa', 'event-tickets' ),
			'KD' => __( 'Kaduna', 'event-tickets' ),
			'KN' => __( 'Kano', 'event-tickets' ),
			'KT' => __( 'Katsina', 'event-tickets' ),
			'KE' => __( 'Kebbi', 'event-tickets' ),
			'KO' => __( 'Kogi', 'event-tickets' ),
			'KW' => __( 'Kwara', 'event-tickets' ),
			'LA' => __( 'Lagos', 'event-tickets' ),
			'NA' => __( 'Nasarawa', 'event-tickets' ),
			'NI' => __( 'Niger', 'event-tickets' ),
			'OG' => __( 'Ogun', 'event-tickets' ),
			'ON' => __( 'Ondo', 'event-tickets' ),
			'OS' => __( 'Osun', 'event-tickets' ),
			'OY' => __( 'Oyo', 'event-tickets' ),
			'PL' => __( 'Plateau', 'event-tickets' ),
			'RI' => __( 'Rivers', 'event-tickets' ),
			'SO' => __( 'Sokoto', 'event-tickets' ),
			'TA' => __( 'Taraba', 'event-tickets' ),
			'YO' => __( 'Yobe', 'event-tickets' ),
			'ZA' => __( 'Zamfara', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_nigerian_states', $states );
	}

	/**
	 * Get Mexico States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_mexico_list() {
		$states = [
			''                    => '',
			'Distrito Federal'    => __( 'Distrito Federal', 'event-tickets' ),
			'Jalisco'             => __( 'Jalisco', 'event-tickets' ),
			'Nuevo Leon'          => __( 'Nuevo León', 'event-tickets' ),
			'Aguascalientes'      => __( 'Aguascalientes', 'event-tickets' ),
			'Baja California'     => __( 'Baja California', 'event-tickets' ),
			'Baja California Sur' => __( 'Baja California Sur', 'event-tickets' ),
			'Campeche'            => __( 'Campeche', 'event-tickets' ),
			'Chiapas'             => __( 'Chiapas', 'event-tickets' ),
			'Chihuahua'           => __( 'Chihuahua', 'event-tickets' ),
			'Coahuila'            => __( 'Coahuila', 'event-tickets' ),
			'Colima'              => __( 'Colima', 'event-tickets' ),
			'Durango'             => __( 'Durango', 'event-tickets' ),
			'Guanajuato'          => __( 'Guanajuato', 'event-tickets' ),
			'Guerrero'            => __( 'Guerrero', 'event-tickets' ),
			'Hidalgo'             => __( 'Hidalgo', 'event-tickets' ),
			'Estado de Mexico'    => __( 'Edo. de México', 'event-tickets' ),
			'Michoacan'           => __( 'Michoacán', 'event-tickets' ),
			'Morelos'             => __( 'Morelos', 'event-tickets' ),
			'Nayarit'             => __( 'Nayarit', 'event-tickets' ),
			'Oaxaca'              => __( 'Oaxaca', 'event-tickets' ),
			'Puebla'              => __( 'Puebla', 'event-tickets' ),
			'Queretaro'           => __( 'Querétaro', 'event-tickets' ),
			'Quintana Roo'        => __( 'Quintana Roo', 'event-tickets' ),
			'San Luis Potosi'     => __( 'San Luis Potosí', 'event-tickets' ),
			'Sinaloa'             => __( 'Sinaloa', 'event-tickets' ),
			'Sonora'              => __( 'Sonora', 'event-tickets' ),
			'Tabasco'             => __( 'Tabasco', 'event-tickets' ),
			'Tamaulipas'          => __( 'Tamaulipas', 'event-tickets' ),
			'Tlaxcala'            => __( 'Tlaxcala', 'event-tickets' ),
			'Veracruz'            => __( 'Veracruz', 'event-tickets' ),
			'Yucatan'             => __( 'Yucatán', 'event-tickets' ),
			'Zacatecas'           => __( 'Zacatecas', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_mexico_states', $states );
	}

	/**
	 * Get Japan States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_japan_list() {
		$states = [
			''     => '',
			'JP01' => __( 'Hokkaido', 'event-tickets' ),
			'JP02' => __( 'Aomori', 'event-tickets' ),
			'JP03' => __( 'Iwate', 'event-tickets' ),
			'JP04' => __( 'Miyagi', 'event-tickets' ),
			'JP05' => __( 'Akita', 'event-tickets' ),
			'JP06' => __( 'Yamagata', 'event-tickets' ),
			'JP07' => __( 'Fukushima', 'event-tickets' ),
			'JP08' => __( 'Ibaraki', 'event-tickets' ),
			'JP09' => __( 'Tochigi', 'event-tickets' ),
			'JP10' => __( 'Gunma', 'event-tickets' ),
			'JP11' => __( 'Saitama', 'event-tickets' ),
			'JP12' => __( 'Chiba', 'event-tickets' ),
			'JP13' => __( 'Tokyo', 'event-tickets' ),
			'JP14' => __( 'Kanagawa', 'event-tickets' ),
			'JP15' => __( 'Niigata', 'event-tickets' ),
			'JP16' => __( 'Toyama', 'event-tickets' ),
			'JP17' => __( 'Ishikawa', 'event-tickets' ),
			'JP18' => __( 'Fukui', 'event-tickets' ),
			'JP19' => __( 'Yamanashi', 'event-tickets' ),
			'JP20' => __( 'Nagano', 'event-tickets' ),
			'JP21' => __( 'Gifu', 'event-tickets' ),
			'JP22' => __( 'Shizuoka', 'event-tickets' ),
			'JP23' => __( 'Aichi', 'event-tickets' ),
			'JP24' => __( 'Mie', 'event-tickets' ),
			'JP25' => __( 'Shiga', 'event-tickets' ),
			'JP26' => __( 'Kyoto', 'event-tickets' ),
			'JP27' => __( 'Osaka', 'event-tickets' ),
			'JP28' => __( 'Hyogo', 'event-tickets' ),
			'JP29' => __( 'Nara', 'event-tickets' ),
			'JP30' => __( 'Wakayama', 'event-tickets' ),
			'JP31' => __( 'Tottori', 'event-tickets' ),
			'JP32' => __( 'Shimane', 'event-tickets' ),
			'JP33' => __( 'Okayama', 'event-tickets' ),
			'JP34' => __( 'Hiroshima', 'event-tickets' ),
			'JP35' => __( 'Yamaguchi', 'event-tickets' ),
			'JP36' => __( 'Tokushima', 'event-tickets' ),
			'JP37' => __( 'Kagawa', 'event-tickets' ),
			'JP38' => __( 'Ehime', 'event-tickets' ),
			'JP39' => __( 'Kochi', 'event-tickets' ),
			'JP40' => __( 'Fukuoka', 'event-tickets' ),
			'JP41' => __( 'Saga', 'event-tickets' ),
			'JP42' => __( 'Nagasaki', 'event-tickets' ),
			'JP43' => __( 'Kumamoto', 'event-tickets' ),
			'JP44' => __( 'Oita', 'event-tickets' ),
			'JP45' => __( 'Miyazaki', 'event-tickets' ),
			'JP46' => __( 'Kagoshima', 'event-tickets' ),
			'JP47' => __( 'Okinawa', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_japan_states', $states );
	}

	/**
	 * Get Italy States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_italy_list() {
		$states = [
			''   => '',
			'AG' => __( 'Agrigento', 'event-tickets' ),
			'AL' => __( 'Alessandria', 'event-tickets' ),
			'AN' => __( 'Ancona', 'event-tickets' ),
			'AO' => __( 'Aosta', 'event-tickets' ),
			'AR' => __( 'Arezzo', 'event-tickets' ),
			'AP' => __( 'Ascoli Piceno', 'event-tickets' ),
			'AT' => __( 'Asti', 'event-tickets' ),
			'AV' => __( 'Avellino', 'event-tickets' ),
			'BA' => __( 'Bari', 'event-tickets' ),
			'BT' => __( 'Barletta-Andria-Trani', 'event-tickets' ),
			'BL' => __( 'Belluno', 'event-tickets' ),
			'BN' => __( 'Benevento', 'event-tickets' ),
			'BG' => __( 'Bergamo', 'event-tickets' ),
			'BI' => __( 'Biella', 'event-tickets' ),
			'BO' => __( 'Bologna', 'event-tickets' ),
			'BZ' => __( 'Bolzano', 'event-tickets' ),
			'BS' => __( 'Brescia', 'event-tickets' ),
			'BR' => __( 'Brindisi', 'event-tickets' ),
			'CA' => __( 'Cagliari', 'event-tickets' ),
			'CL' => __( 'Caltanissetta', 'event-tickets' ),
			'CB' => __( 'Campobasso', 'event-tickets' ),
			'CI' => __( 'Carbonia-Iglesias', 'event-tickets' ),
			'CE' => __( 'Caserta', 'event-tickets' ),
			'CT' => __( 'Catania', 'event-tickets' ),
			'CZ' => __( 'Catanzaro', 'event-tickets' ),
			'CH' => __( 'Chieti', 'event-tickets' ),
			'CO' => __( 'Como', 'event-tickets' ),
			'CS' => __( 'Cosenza', 'event-tickets' ),
			'CR' => __( 'Cremona', 'event-tickets' ),
			'KR' => __( 'Crotone', 'event-tickets' ),
			'CN' => __( 'Cuneo', 'event-tickets' ),
			'EN' => __( 'Enna', 'event-tickets' ),
			'FM' => __( 'Fermo', 'event-tickets' ),
			'FE' => __( 'Ferrara', 'event-tickets' ),
			'FI' => __( 'Firenze', 'event-tickets' ),
			'FG' => __( 'Foggia', 'event-tickets' ),
			'FC' => __( 'Forlì-Cesena', 'event-tickets' ),
			'FR' => __( 'Frosinone', 'event-tickets' ),
			'GE' => __( 'Genova', 'event-tickets' ),
			'GO' => __( 'Gorizia', 'event-tickets' ),
			'GR' => __( 'Grosseto', 'event-tickets' ),
			'IM' => __( 'Imperia', 'event-tickets' ),
			'IS' => __( 'Isernia', 'event-tickets' ),
			'SP' => __( 'La Spezia', 'event-tickets' ),
			'AQ' => __( "L'Aquila", 'event-tickets' ),
			'LT' => __( 'Latina', 'event-tickets' ),
			'LE' => __( 'Lecce', 'event-tickets' ),
			'LC' => __( 'Lecco', 'event-tickets' ),
			'LI' => __( 'Livorno', 'event-tickets' ),
			'LO' => __( 'Lodi', 'event-tickets' ),
			'LU' => __( 'Lucca', 'event-tickets' ),
			'MC' => __( 'Macerata', 'event-tickets' ),
			'MN' => __( 'Mantova', 'event-tickets' ),
			'MS' => __( 'Massa-Carrara', 'event-tickets' ),
			'MT' => __( 'Matera', 'event-tickets' ),
			'ME' => __( 'Messina', 'event-tickets' ),
			'MI' => __( 'Milano', 'event-tickets' ),
			'MO' => __( 'Modena', 'event-tickets' ),
			'MB' => __( 'Monza e della Brianza', 'event-tickets' ),
			'NA' => __( 'Napoli', 'event-tickets' ),
			'NO' => __( 'Novara', 'event-tickets' ),
			'NU' => __( 'Nuoro', 'event-tickets' ),
			'OT' => __( 'Olbia-Tempio', 'event-tickets' ),
			'OR' => __( 'Oristano', 'event-tickets' ),
			'PD' => __( 'Padova', 'event-tickets' ),
			'PA' => __( 'Palermo', 'event-tickets' ),
			'PR' => __( 'Parma', 'event-tickets' ),
			'PV' => __( 'Pavia', 'event-tickets' ),
			'PG' => __( 'Perugia', 'event-tickets' ),
			'PU' => __( 'Pesaro e Urbino', 'event-tickets' ),
			'PE' => __( 'Pescara', 'event-tickets' ),
			'PC' => __( 'Piacenza', 'event-tickets' ),
			'PI' => __( 'Pisa', 'event-tickets' ),
			'PT' => __( 'Pistoia', 'event-tickets' ),
			'PN' => __( 'Pordenone', 'event-tickets' ),
			'PZ' => __( 'Potenza', 'event-tickets' ),
			'PO' => __( 'Prato', 'event-tickets' ),
			'RG' => __( 'Ragusa', 'event-tickets' ),
			'RA' => __( 'Ravenna', 'event-tickets' ),
			'RC' => __( 'Reggio Calabria', 'event-tickets' ),
			'RE' => __( 'Reggio Emilia', 'event-tickets' ),
			'RI' => __( 'Rieti', 'event-tickets' ),
			'RN' => __( 'Rimini', 'event-tickets' ),
			'RM' => __( 'Roma', 'event-tickets' ),
			'RO' => __( 'Rovigo', 'event-tickets' ),
			'SA' => __( 'Salerno', 'event-tickets' ),
			'VS' => __( 'Medio Campidano', 'event-tickets' ),
			'SS' => __( 'Sassari', 'event-tickets' ),
			'SV' => __( 'Savona', 'event-tickets' ),
			'SI' => __( 'Siena', 'event-tickets' ),
			'SR' => __( 'Siracusa', 'event-tickets' ),
			'SO' => __( 'Sondrio', 'event-tickets' ),
			'TA' => __( 'Taranto', 'event-tickets' ),
			'TE' => __( 'Teramo', 'event-tickets' ),
			'TR' => __( 'Terni', 'event-tickets' ),
			'TO' => __( 'Torino', 'event-tickets' ),
			'OG' => __( 'Ogliastra', 'event-tickets' ),
			'TP' => __( 'Trapani', 'event-tickets' ),
			'TN' => __( 'Trento', 'event-tickets' ),
			'TV' => __( 'Treviso', 'event-tickets' ),
			'TS' => __( 'Trieste', 'event-tickets' ),
			'UD' => __( 'Udine', 'event-tickets' ),
			'VA' => __( 'Varese', 'event-tickets' ),
			'VE' => __( 'Venezia', 'event-tickets' ),
			'VB' => __( 'Verbano-Cusio-Ossola', 'event-tickets' ),
			'VC' => __( 'Vercelli', 'event-tickets' ),
			'VR' => __( 'Verona', 'event-tickets' ),
			'VV' => __( 'Vibo Valentia', 'event-tickets' ),
			'VI' => __( 'Vicenza', 'event-tickets' ),
			'VT' => __( 'Viterbo', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_italy_states', $states );
	}

	/**
	 * Get Iran States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_iran_list() {
		$states = [
			''    => '',
			'KHZ' => __( 'Khuzestan  (خوزستان)', 'event-tickets' ),
			'THR' => __( 'Tehran  (تهران)', 'event-tickets' ),
			'ILM' => __( 'Ilaam (ایلام)', 'event-tickets' ),
			'BHR' => __( 'Bushehr (بوشهر)', 'event-tickets' ),
			'ADL' => __( 'Ardabil (اردبیل)', 'event-tickets' ),
			'ESF' => __( 'Isfahan (اصفهان)', 'event-tickets' ),
			'YZD' => __( 'Yazd (یزد)', 'event-tickets' ),
			'KRH' => __( 'Kermanshah (کرمانشاه)', 'event-tickets' ),
			'KRN' => __( 'Kerman (کرمان)', 'event-tickets' ),
			'HDN' => __( 'Hamadan (همدان)', 'event-tickets' ),
			'GZN' => __( 'Ghazvin (قزوین)', 'event-tickets' ),
			'ZJN' => __( 'Zanjan (زنجان)', 'event-tickets' ),
			'LRS' => __( 'Luristan (لرستان)', 'event-tickets' ),
			'ABZ' => __( 'Alborz (البرز)', 'event-tickets' ),
			'EAZ' => __( 'East Azarbaijan (آذربایجان شرقی)', 'event-tickets' ),
			'WAZ' => __( 'West Azarbaijan (آذربایجان غربی)', 'event-tickets' ),
			'CHB' => __( 'Chaharmahal and Bakhtiari (چهارمحال و بختیاری)', 'event-tickets' ),
			'SKH' => __( 'South Khorasan (خراسان جنوبی)', 'event-tickets' ),
			'RKH' => __( 'Razavi Khorasan (خراسان رضوی)', 'event-tickets' ),
			'NKH' => __( 'North Khorasan (خراسان جنوبی)', 'event-tickets' ),
			'SMN' => __( 'Semnan (سمنان)', 'event-tickets' ),
			'FRS' => __( 'Fars (فارس)', 'event-tickets' ),
			'QHM' => __( 'Qom (قم)', 'event-tickets' ),
			'KRD' => __( 'Kurdistan / کردستان)', 'event-tickets' ),
			'KBD' => __( 'Kohgiluyeh and BoyerAhmad (کهگیلوییه و بویراحمد)', 'event-tickets' ),
			'GLS' => __( 'Golestan (گلستان)', 'event-tickets' ),
			'GIL' => __( 'Gilan (گیلان)', 'event-tickets' ),
			'MZN' => __( 'Mazandaran (مازندران)', 'event-tickets' ),
			'MKZ' => __( 'Markazi (مرکزی)', 'event-tickets' ),
			'HRZ' => __( 'Hormozgan (هرمزگان)', 'event-tickets' ),
			'SBN' => __( 'Sistan and Baluchestan (سیستان و بلوچستان)', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_iran_states', $states );
	}

	/**
	 * Get Ireland States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_ireland_list() {
		$states = [
			''   => '',
			'AN' => __( 'Antrim', 'event-tickets' ),
			'AR' => __( 'Armagh', 'event-tickets' ),
			'CE' => __( 'Clare', 'event-tickets' ),
			'CK' => __( 'Cork', 'event-tickets' ),
			'CN' => __( 'Cavan', 'event-tickets' ),
			'CW' => __( 'Carlow', 'event-tickets' ),
			'DL' => __( 'Donegal', 'event-tickets' ),
			'DN' => __( 'Dublin', 'event-tickets' ),
			'DO' => __( 'Down', 'event-tickets' ),
			'DY' => __( 'Derry', 'event-tickets' ),
			'FM' => __( 'Fermanagh', 'event-tickets' ),
			'GY' => __( 'Galway', 'event-tickets' ),
			'KE' => __( 'Kildare', 'event-tickets' ),
			'KK' => __( 'Kilkenny', 'event-tickets' ),
			'KY' => __( 'Kerry', 'event-tickets' ),
			'LD' => __( 'Longford', 'event-tickets' ),
			'LH' => __( 'Louth', 'event-tickets' ),
			'LK' => __( 'Limerick', 'event-tickets' ),
			'LM' => __( 'Leitrim', 'event-tickets' ),
			'LS' => __( 'Laois', 'event-tickets' ),
			'MH' => __( 'Meath', 'event-tickets' ),
			'MN' => __( 'Monaghan', 'event-tickets' ),
			'MO' => __( 'Mayo', 'event-tickets' ),
			'OY' => __( 'Offaly', 'event-tickets' ),
			'RN' => __( 'Roscommon', 'event-tickets' ),
			'SO' => __( 'Sligo', 'event-tickets' ),
			'TR' => __( 'Tyrone', 'event-tickets' ),
			'TY' => __( 'Tipperary', 'event-tickets' ),
			'WD' => __( 'Waterford', 'event-tickets' ),
			'WH' => __( 'Westmeath', 'event-tickets' ),
			'WW' => __( 'Wicklow', 'event-tickets' ),
			'WX' => __( 'Wexford', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_ireland_states', $states );
	}

	/**
	 * Get Greek States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_greek_list() {
		$states = [
			''  => '',
			'I' => __( 'Αττική', 'event-tickets' ),
			'A' => __( 'Ανατολική Μακεδονία και Θράκη', 'event-tickets' ),
			'B' => __( 'Κεντρική Μακεδονία', 'event-tickets' ),
			'C' => __( 'Δυτική Μακεδονία', 'event-tickets' ),
			'D' => __( 'Ήπειρος', 'event-tickets' ),
			'E' => __( 'Θεσσαλία', 'event-tickets' ),
			'F' => __( 'Ιόνιοι Νήσοι', 'event-tickets' ),
			'G' => __( 'Δυτική Ελλάδα', 'event-tickets' ),
			'H' => __( 'Στερεά Ελλάδα', 'event-tickets' ),
			'J' => __( 'Πελοπόννησος', 'event-tickets' ),
			'K' => __( 'Βόρειο Αιγαίο', 'event-tickets' ),
			'L' => __( 'Νότιο Αιγαίο', 'event-tickets' ),
			'M' => __( 'Κρήτη', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_greek_states', $states );
	}

	/**
	 * Get bolivian States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_bolivian_list() {
		$states = [
			''  => '',
			'B' => __( 'Chuquisaca', 'event-tickets' ),
			'H' => __( 'Beni', 'event-tickets' ),
			'C' => __( 'Cochabamba', 'event-tickets' ),
			'L' => __( 'La Paz', 'event-tickets' ),
			'O' => __( 'Oruro', 'event-tickets' ),
			'N' => __( 'Pando', 'event-tickets' ),
			'P' => __( 'Potosí', 'event-tickets' ),
			'S' => __( 'Santa Cruz', 'event-tickets' ),
			'T' => __( 'Tarija', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_bolivian_states', $states );
	}

	/**
	 * Get Bulgarian States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_bulgarian_list() {
		$states = [
			''      => '',
			'BG-01' => __( 'Blagoevgrad', 'event-tickets' ),
			'BG-02' => __( 'Burgas', 'event-tickets' ),
			'BG-08' => __( 'Dobrich', 'event-tickets' ),
			'BG-07' => __( 'Gabrovo', 'event-tickets' ),
			'BG-26' => __( 'Haskovo', 'event-tickets' ),
			'BG-09' => __( 'Kardzhali', 'event-tickets' ),
			'BG-10' => __( 'Kyustendil', 'event-tickets' ),
			'BG-11' => __( 'Lovech', 'event-tickets' ),
			'BG-12' => __( 'Montana', 'event-tickets' ),
			'BG-13' => __( 'Pazardzhik', 'event-tickets' ),
			'BG-14' => __( 'Pernik', 'event-tickets' ),
			'BG-15' => __( 'Pleven', 'event-tickets' ),
			'BG-16' => __( 'Plovdiv', 'event-tickets' ),
			'BG-17' => __( 'Razgrad', 'event-tickets' ),
			'BG-18' => __( 'Ruse', 'event-tickets' ),
			'BG-27' => __( 'Shumen', 'event-tickets' ),
			'BG-19' => __( 'Silistra', 'event-tickets' ),
			'BG-20' => __( 'Sliven', 'event-tickets' ),
			'BG-21' => __( 'Smolyan', 'event-tickets' ),
			'BG-23' => __( 'Sofia', 'event-tickets' ),
			'BG-22' => __( 'Sofia-Grad', 'event-tickets' ),
			'BG-24' => __( 'Stara Zagora', 'event-tickets' ),
			'BG-25' => __( 'Targovishte', 'event-tickets' ),
			'BG-03' => __( 'Varna', 'event-tickets' ),
			'BG-04' => __( 'Veliko Tarnovo', 'event-tickets' ),
			'BG-05' => __( 'Vidin', 'event-tickets' ),
			'BG-06' => __( 'Vratsa', 'event-tickets' ),
			'BG-28' => __( 'Yambol', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_bulgarian_states', $states );
	}

	/**
	 * Get Bangladeshi States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_bangladeshi_list() {
		$states = [
			''     => '',
			'BAG'  => __( 'Bagerhat', 'event-tickets' ),
			'BAN'  => __( 'Bandarban', 'event-tickets' ),
			'BAR'  => __( 'Barguna', 'event-tickets' ),
			'BARI' => __( 'Barisal', 'event-tickets' ),
			'BHO'  => __( 'Bhola', 'event-tickets' ),
			'BOG'  => __( 'Bogra', 'event-tickets' ),
			'BRA'  => __( 'Brahmanbaria', 'event-tickets' ),
			'CHA'  => __( 'Chandpur', 'event-tickets' ),
			'CHI'  => __( 'Chittagong', 'event-tickets' ),
			'CHU'  => __( 'Chuadanga', 'event-tickets' ),
			'COM'  => __( 'Comilla', 'event-tickets' ),
			'COX'  => __( "Cox's Bazar", 'event-tickets' ),
			'DHA'  => __( 'Dhaka', 'event-tickets' ),
			'DIN'  => __( 'Dinajpur', 'event-tickets' ),
			'FAR'  => __( 'Faridpur ', 'event-tickets' ),
			'FEN'  => __( 'Feni', 'event-tickets' ),
			'GAI'  => __( 'Gaibandha', 'event-tickets' ),
			'GAZI' => __( 'Gazipur', 'event-tickets' ),
			'GOP'  => __( 'Gopalganj', 'event-tickets' ),
			'HAB'  => __( 'Habiganj', 'event-tickets' ),
			'JAM'  => __( 'Jamalpur', 'event-tickets' ),
			'JES'  => __( 'Jessore', 'event-tickets' ),
			'JHA'  => __( 'Jhalokati', 'event-tickets' ),
			'JHE'  => __( 'Jhenaidah', 'event-tickets' ),
			'JOY'  => __( 'Joypurhat', 'event-tickets' ),
			'KHA'  => __( 'Khagrachhari', 'event-tickets' ),
			'KHU'  => __( 'Khulna', 'event-tickets' ),
			'KIS'  => __( 'Kishoreganj', 'event-tickets' ),
			'KUR'  => __( 'Kurigram', 'event-tickets' ),
			'KUS'  => __( 'Kushtia', 'event-tickets' ),
			'LAK'  => __( 'Lakshmipur', 'event-tickets' ),
			'LAL'  => __( 'Lalmonirhat', 'event-tickets' ),
			'MAD'  => __( 'Madaripur', 'event-tickets' ),
			'MAG'  => __( 'Magura', 'event-tickets' ),
			'MAN'  => __( 'Manikganj ', 'event-tickets' ),
			'MEH'  => __( 'Meherpur', 'event-tickets' ),
			'MOU'  => __( 'Moulvibazar', 'event-tickets' ),
			'MUN'  => __( 'Munshiganj', 'event-tickets' ),
			'MYM'  => __( 'Mymensingh', 'event-tickets' ),
			'NAO'  => __( 'Naogaon', 'event-tickets' ),
			'NAR'  => __( 'Narail', 'event-tickets' ),
			'NARG' => __( 'Narayanganj', 'event-tickets' ),
			'NARD' => __( 'Narsingdi', 'event-tickets' ),
			'NAT'  => __( 'Natore', 'event-tickets' ),
			'NAW'  => __( 'Nawabganj', 'event-tickets' ),
			'NET'  => __( 'Netrakona', 'event-tickets' ),
			'NIL'  => __( 'Nilphamari', 'event-tickets' ),
			'NOA'  => __( 'Noakhali', 'event-tickets' ),
			'PAB'  => __( 'Pabna', 'event-tickets' ),
			'PAN'  => __( 'Panchagarh', 'event-tickets' ),
			'PAT'  => __( 'Patuakhali', 'event-tickets' ),
			'PIR'  => __( 'Pirojpur', 'event-tickets' ),
			'RAJB' => __( 'Rajbari', 'event-tickets' ),
			'RAJ'  => __( 'Rajshahi', 'event-tickets' ),
			'RAN'  => __( 'Rangamati', 'event-tickets' ),
			'RANP' => __( 'Rangpur', 'event-tickets' ),
			'SAT'  => __( 'Satkhira', 'event-tickets' ),
			'SHA'  => __( 'Shariatpur', 'event-tickets' ),
			'SHE'  => __( 'Sherpur', 'event-tickets' ),
			'SIR'  => __( 'Sirajganj', 'event-tickets' ),
			'SUN'  => __( 'Sunamganj', 'event-tickets' ),
			'SYL'  => __( 'Sylhet', 'event-tickets' ),
			'TAN'  => __( 'Tangail', 'event-tickets' ),
			'THA'  => __( 'Thakurgaon', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_bangladeshi_states', $states );
	}

	/**
	 * Get Argentina States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_argentina_list() {
		$states = [
			''  => '',
			'C' => __( 'Ciudad Aut&oacute;noma de Buenos Aires', 'event-tickets' ),
			'B' => __( 'Buenos Aires', 'event-tickets' ),
			'K' => __( 'Catamarca', 'event-tickets' ),
			'H' => __( 'Chaco', 'event-tickets' ),
			'U' => __( 'Chubut', 'event-tickets' ),
			'X' => __( 'C&oacute;rdoba', 'event-tickets' ),
			'W' => __( 'Corrientes', 'event-tickets' ),
			'E' => __( 'Entre R&iacute;os', 'event-tickets' ),
			'P' => __( 'Formosa', 'event-tickets' ),
			'Y' => __( 'Jujuy', 'event-tickets' ),
			'L' => __( 'La Pampa', 'event-tickets' ),
			'F' => __( 'La Rioja', 'event-tickets' ),
			'M' => __( 'Mendoza', 'event-tickets' ),
			'N' => __( 'Misiones', 'event-tickets' ),
			'Q' => __( 'Neuqu&eacute;n', 'event-tickets' ),
			'R' => __( 'R&iacute;o Negro', 'event-tickets' ),
			'A' => __( 'Salta', 'event-tickets' ),
			'J' => __( 'San Juan', 'event-tickets' ),
			'D' => __( 'San Luis', 'event-tickets' ),
			'Z' => __( 'Santa Cruz', 'event-tickets' ),
			'S' => __( 'Santa Fe', 'event-tickets' ),
			'G' => __( 'Santiago del Estero', 'event-tickets' ),
			'V' => __( 'Tierra del Fuego', 'event-tickets' ),
			'T' => __( 'Tucum&aacute;n', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_argentina_states', $states );
	}

	/**
	 * Get States List
	 *
	 * @since 5.2.0
	 *
	 * @return      array
	 */
	public function get_us_list() {
		$states = [
			''   => '',
			'AL' => 'Alabama',
			'AK' => 'Alaska',
			'AZ' => 'Arizona',
			'AR' => 'Arkansas',
			'CA' => 'California',
			'CO' => 'Colorado',
			'CT' => 'Connecticut',
			'DE' => 'Delaware',
			'DC' => 'District of Columbia',
			'FL' => 'Florida',
			'GA' => 'Georgia',
			'HI' => 'Hawaii',
			'ID' => 'Idaho',
			'IL' => 'Illinois',
			'IN' => 'Indiana',
			'IA' => 'Iowa',
			'KS' => 'Kansas',
			'KY' => 'Kentucky',
			'LA' => 'Louisiana',
			'ME' => 'Maine',
			'MD' => 'Maryland',
			'MA' => 'Massachusetts',
			'MI' => 'Michigan',
			'MN' => 'Minnesota',
			'MS' => 'Mississippi',
			'MO' => 'Missouri',
			'MT' => 'Montana',
			'NE' => 'Nebraska',
			'NV' => 'Nevada',
			'NH' => 'New Hampshire',
			'NJ' => 'New Jersey',
			'NM' => 'New Mexico',
			'NY' => 'New York',
			'NC' => 'North Carolina',
			'ND' => 'North Dakota',
			'OH' => 'Ohio',
			'OK' => 'Oklahoma',
			'OR' => 'Oregon',
			'PA' => 'Pennsylvania',
			'RI' => 'Rhode Island',
			'SC' => 'South Carolina',
			'SD' => 'South Dakota',
			'TN' => 'Tennessee',
			'TX' => 'Texas',
			'UT' => 'Utah',
			'VT' => 'Vermont',
			'VA' => 'Virginia',
			'WA' => 'Washington',
			'WV' => 'West Virginia',
			'WI' => 'Wisconsin',
			'WY' => 'Wyoming',
			'AS' => 'American Samoa',
			'CZ' => 'Canal Zone',
			'CM' => 'Commonwealth of the Northern Mariana Islands',
			'FM' => 'Federated States of Micronesia',
			'GU' => 'Guam',
			'MH' => 'Marshall Islands',
			'MP' => 'Northern Mariana Islands',
			'PW' => 'Palau',
			'PI' => 'Philippine Islands',
			'PR' => 'Puerto Rico',
			'TT' => 'Trust Territory of the Pacific Islands',
			'VI' => 'Virgin Islands',
			'AA' => 'Armed Forces - Americas',
			'AE' => 'Armed Forces - Europe, Canada, Middle East, Africa',
			'AP' => 'Armed Forces - Pacific',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_us_states', $states );
	}

	/**
	 * Get Provinces List
	 *
	 * @since 5.2.0
	 *
	 * @return      array
	 */
	public function get_provinces_list() {
		$provinces = [
			''   => '',
			'AB' => esc_html__( 'Alberta', 'event-tickets' ),
			'BC' => esc_html__( 'British Columbia', 'event-tickets' ),
			'MB' => esc_html__( 'Manitoba', 'event-tickets' ),
			'NB' => esc_html__( 'New Brunswick', 'event-tickets' ),
			'NL' => esc_html__( 'Newfoundland and Labrador', 'event-tickets' ),
			'NS' => esc_html__( 'Nova Scotia', 'event-tickets' ),
			'NT' => esc_html__( 'Northwest Territories', 'event-tickets' ),
			'NU' => esc_html__( 'Nunavut', 'event-tickets' ),
			'ON' => esc_html__( 'Ontario', 'event-tickets' ),
			'PE' => esc_html__( 'Prince Edward Island', 'event-tickets' ),
			'QC' => esc_html__( 'Quebec', 'event-tickets' ),
			'SK' => esc_html__( 'Saskatchewan', 'event-tickets' ),
			'YT' => esc_html__( 'Yukon', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_canada_provinces', $provinces );
	}

	/**
	 * Get Australian States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_australian_list() {
		$states = [
			''    => '',
			'ACT' => 'Australian Capital Territory',
			'NSW' => 'New South Wales',
			'NT'  => 'Northern Territory',
			'QLD' => 'Queensland',
			'SA'  => 'South Australia',
			'TAS' => 'Tasmania',
			'VIC' => 'Victoria',
			'WA'  => 'Western Australia',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_australian_states', $states );
	}

	/**
	 * Get Brazil States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_brazil_list() {
		$states = [
			''   => '',
			'AC' => 'Acre',
			'AL' => 'Alagoas',
			'AP' => 'Amap&aacute;',
			'AM' => 'Amazonas',
			'BA' => 'Bahia',
			'CE' => 'Cear&aacute;',
			'DF' => 'Distrito Federal',
			'ES' => 'Esp&iacute;rito Santo',
			'GO' => 'Goi&aacute;s',
			'MA' => 'Maranh&atilde;o',
			'MT' => 'Mato Grosso',
			'MS' => 'Mato Grosso do Sul',
			'MG' => 'Minas Gerais',
			'PA' => 'Par&aacute;',
			'PB' => 'Para&iacute;ba',
			'PR' => 'Paran&aacute;',
			'PE' => 'Pernambuco',
			'PI' => 'Piau&iacute;',
			'RJ' => 'Rio de Janeiro',
			'RN' => 'Rio Grande do Norte',
			'RS' => 'Rio Grande do Sul',
			'RO' => 'Rond&ocirc;nia',
			'RR' => 'Roraima',
			'SC' => 'Santa Catarina',
			'SP' => 'S&atilde;o Paulo',
			'SE' => 'Sergipe',
			'TO' => 'Tocantins',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_brazil_states', $states );
	}

	/**
	 * Get Hong Kong States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_hong_kong_list() {
		$states = [
			''                => '',
			'HONG KONG'       => 'Hong Kong Island',
			'KOWLOON'         => 'Kowloon',
			'NEW TERRITORIES' => 'New Territories',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_hong_kong_states', $states );
	}

	/**
	 * Get Hungary States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_hungary_list() {
		$states = [
			''   => '',
			'BK' => 'Bács-Kiskun',
			'BE' => 'Békés',
			'BA' => 'Baranya',
			'BZ' => 'Borsod-Abaúj-Zemplén',
			'BU' => 'Budapest',
			'CS' => 'Csongrád',
			'FE' => 'Fejér',
			'GS' => 'Győr-Moson-Sopron',
			'HB' => 'Hajdú-Bihar',
			'HE' => 'Heves',
			'JN' => 'Jász-Nagykun-Szolnok',
			'KE' => 'Komárom-Esztergom',
			'NO' => 'Nógrád',
			'PE' => 'Pest',
			'SO' => 'Somogy',
			'SZ' => 'Szabolcs-Szatmár-Bereg',
			'TO' => 'Tolna',
			'VA' => 'Vas',
			'VE' => 'Veszprém',
			'ZA' => 'Zala',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_hungary_states', $states );
	}

	/**
	 * Get Chinese States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_chinese_list() {
		$states = [
			''     => '',
			'CN1'  => 'Yunnan / &#20113;&#21335;',
			'CN2'  => 'Beijing / &#21271;&#20140;',
			'CN3'  => 'Tianjin / &#22825;&#27941;',
			'CN4'  => 'Hebei / &#27827;&#21271;',
			'CN5'  => 'Shanxi / &#23665;&#35199;',
			'CN6'  => 'Inner Mongolia / &#20839;&#33945;&#21476;',
			'CN7'  => 'Liaoning / &#36797;&#23425;',
			'CN8'  => 'Jilin / &#21513;&#26519;',
			'CN9'  => 'Heilongjiang / &#40657;&#40857;&#27743;',
			'CN10' => 'Shanghai / &#19978;&#28023;',
			'CN11' => 'Jiangsu / &#27743;&#33487;',
			'CN12' => 'Zhejiang / &#27993;&#27743;',
			'CN13' => 'Anhui / &#23433;&#24509;',
			'CN14' => 'Fujian / &#31119;&#24314;',
			'CN15' => 'Jiangxi / &#27743;&#35199;',
			'CN16' => 'Shandong / &#23665;&#19996;',
			'CN17' => 'Henan / &#27827;&#21335;',
			'CN18' => 'Hubei / &#28246;&#21271;',
			'CN19' => 'Hunan / &#28246;&#21335;',
			'CN20' => 'Guangdong / &#24191;&#19996;',
			'CN21' => 'Guangxi Zhuang / &#24191;&#35199;&#22766;&#26063;',
			'CN22' => 'Hainan / &#28023;&#21335;',
			'CN23' => 'Chongqing / &#37325;&#24198;',
			'CN24' => 'Sichuan / &#22235;&#24029;',
			'CN25' => 'Guizhou / &#36149;&#24030;',
			'CN26' => 'Shaanxi / &#38485;&#35199;',
			'CN27' => 'Gansu / &#29976;&#32899;',
			'CN28' => 'Qinghai / &#38738;&#28023;',
			'CN29' => 'Ningxia Hui / &#23425;&#22799;',
			'CN30' => 'Macau / &#28595;&#38376;',
			'CN31' => 'Tibet / &#35199;&#34255;',
			'CN32' => 'Xinjiang / &#26032;&#30086;',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_chinese_states', $states );
	}

	/**
	 * Get New Zealand States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_new_zealand_list() {
		$states = [
			''   => '',
			'AK' => 'Auckland',
			'BP' => 'Bay of Plenty',
			'CT' => 'Canterbury',
			'HB' => 'Hawke&rsquo;s Bay',
			'MW' => 'Manawatu-Wanganui',
			'MB' => 'Marlborough',
			'NS' => 'Nelson',
			'NL' => 'Northland',
			'OT' => 'Otago',
			'SL' => 'Southland',
			'TK' => 'Taranaki',
			'TM' => 'Tasman',
			'WA' => 'Waikato',
			'WE' => 'Wellington',
			'WC' => 'West Coast',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_new_zealand_states', $states );
	}

	/**
	 * Get Indonesian States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_indonesian_list() {
		$states = [
			''   => '',
			'AC' => 'Daerah Istimewa Aceh',
			'SU' => 'Sumatera Utara',
			'SB' => 'Sumatera Barat',
			'RI' => 'Riau',
			'KR' => 'Kepulauan Riau',
			'JA' => 'Jambi',
			'SS' => 'Sumatera Selatan',
			'BB' => 'Bangka Belitung',
			'BE' => 'Bengkulu',
			'LA' => 'Lampung',
			'JK' => 'DKI Jakarta',
			'JB' => 'Jawa Barat',
			'BT' => 'Banten',
			'JT' => 'Jawa Tengah',
			'JI' => 'Jawa Timur',
			'YO' => 'Daerah Istimewa Yogyakarta',
			'BA' => 'Bali',
			'NB' => 'Nusa Tenggara Barat',
			'NT' => 'Nusa Tenggara Timur',
			'KB' => 'Kalimantan Barat',
			'KT' => 'Kalimantan Tengah',
			'KI' => 'Kalimantan Timur',
			'KS' => 'Kalimantan Selatan',
			'KU' => 'Kalimantan Utara',
			'SA' => 'Sulawesi Utara',
			'ST' => 'Sulawesi Tengah',
			'SG' => 'Sulawesi Tenggara',
			'SR' => 'Sulawesi Barat',
			'SN' => 'Sulawesi Selatan',
			'GO' => 'Gorontalo',
			'MA' => 'Maluku',
			'MU' => 'Maluku Utara',
			'PA' => 'Papua',
			'PB' => 'Papua Barat',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_indonesia_states', $states );
	}

	/**
	 * Get Indian States
	 *
	 * @since 5.2.0
	 *
	 *
	 * @return array $states A list of states
	 */
	public function get_indian_list() {
		$states = [
			''   => '',
			'AP' => 'Andhra Pradesh',
			'AR' => 'Arunachal Pradesh',
			'AS' => 'Assam',
			'BR' => 'Bihar',
			'CT' => 'Chhattisgarh',
			'GA' => 'Goa',
			'GJ' => 'Gujarat',
			'HR' => 'Haryana',
			'HP' => 'Himachal Pradesh',
			'JK' => 'Jammu and Kashmir',
			'JH' => 'Jharkhand',
			'KA' => 'Karnataka',
			'KL' => 'Kerala',
			'MP' => 'Madhya Pradesh',
			'MH' => 'Maharashtra',
			'MN' => 'Manipur',
			'ML' => 'Meghalaya',
			'MZ' => 'Mizoram',
			'NL' => 'Nagaland',
			'OR' => 'Odisha',
			'PB' => 'Punjab',
			'RJ' => 'Rajasthan',
			'SK' => 'Sikkim',
			'TN' => 'Tamil Nadu',
			'TG' => 'Telangana',
			'TR' => 'Tripura',
			'UT' => 'Uttarakhand',
			'UP' => 'Uttar Pradesh',
			'WB' => 'West Bengal',
			'AN' => 'Andaman and Nicobar Islands',
			'CH' => 'Chandigarh',
			'DN' => 'Dadar and Nagar Haveli',
			'DD' => 'Daman and Diu',
			'DL' => 'Delhi',
			'LD' => 'Lakshadweep',
			'PY' => 'Pondicherry (Puducherry)',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_indian_states', $states );
	}

	/**
	 * Get Malaysian States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_malaysian_list() {
		$states = [
			''    => '',
			'JHR' => 'Johor',
			'KDH' => 'Kedah',
			'KTN' => 'Kelantan',
			'MLK' => 'Melaka',
			'NSN' => 'Negeri Sembilan',
			'PHG' => 'Pahang',
			'PRK' => 'Perak',
			'PLS' => 'Perlis',
			'PNG' => 'Pulau Pinang',
			'SBH' => 'Sabah',
			'SWK' => 'Sarawak',
			'SGR' => 'Selangor',
			'TRG' => 'Terengganu',
			'KUL' => 'W.P. Kuala Lumpur',
			'LBN' => 'W.P. Labuan',
			'PJY' => 'W.P. Putrajaya',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_malaysian_states', $states );
	}

	/**
	 * Get South African States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_south_african_list() {
		$states = [
			''    => '',
			'EC'  => 'Eastern Cape',
			'FS'  => 'Free State',
			'GP'  => 'Gauteng',
			'KZN' => 'KwaZulu-Natal',
			'LP'  => 'Limpopo',
			'MP'  => 'Mpumalanga',
			'NC'  => 'Northern Cape',
			'NW'  => 'North West',
			'WC'  => 'Western Cape',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_south_african_states', $states );
	}

	/**
	 * Get Thailand States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_thailand_list() {
		$states = [
			''      => '',
			'TH-37' => 'Amnat Charoen (&#3629;&#3635;&#3609;&#3634;&#3592;&#3648;&#3592;&#3619;&#3636;&#3597;)',
			'TH-15' => 'Ang Thong (&#3629;&#3656;&#3634;&#3591;&#3607;&#3629;&#3591;)',
			'TH-14' => 'Ayutthaya (&#3614;&#3619;&#3632;&#3609;&#3588;&#3619;&#3624;&#3619;&#3637;&#3629;&#3618;&#3640;&#3608;&#3618;&#3634;)',
			'TH-10' => 'Bangkok (&#3585;&#3619;&#3640;&#3591;&#3648;&#3607;&#3614;&#3617;&#3627;&#3634;&#3609;&#3588;&#3619;)',
			'TH-38' => 'Bueng Kan (&#3610;&#3638;&#3591;&#3585;&#3634;&#3628;)',
			'TH-31' => 'Buri Ram (&#3610;&#3640;&#3619;&#3637;&#3619;&#3633;&#3617;&#3618;&#3660;)',
			'TH-24' => 'Chachoengsao (&#3593;&#3632;&#3648;&#3594;&#3636;&#3591;&#3648;&#3607;&#3619;&#3634;)',
			'TH-18' => 'Chai Nat (&#3594;&#3633;&#3618;&#3609;&#3634;&#3607;)',
			'TH-36' => 'Chaiyaphum (&#3594;&#3633;&#3618;&#3616;&#3641;&#3617;&#3636;)',
			'TH-22' => 'Chanthaburi (&#3592;&#3633;&#3609;&#3607;&#3610;&#3640;&#3619;&#3637;)',
			'TH-50' => 'Chiang Mai (&#3648;&#3594;&#3637;&#3618;&#3591;&#3651;&#3627;&#3617;&#3656;)',
			'TH-57' => 'Chiang Rai (&#3648;&#3594;&#3637;&#3618;&#3591;&#3619;&#3634;&#3618;)',
			'TH-20' => 'Chonburi (&#3594;&#3621;&#3610;&#3640;&#3619;&#3637;)',
			'TH-86' => 'Chumphon (&#3594;&#3640;&#3617;&#3614;&#3619;)',
			'TH-46' => 'Kalasin (&#3585;&#3634;&#3628;&#3626;&#3636;&#3609;&#3608;&#3640;&#3660;)',
			'TH-62' => 'Kamphaeng Phet (&#3585;&#3635;&#3649;&#3614;&#3591;&#3648;&#3614;&#3594;&#3619;)',
			'TH-71' => 'Kanchanaburi (&#3585;&#3634;&#3597;&#3592;&#3609;&#3610;&#3640;&#3619;&#3637;)',
			'TH-40' => 'Khon Kaen (&#3586;&#3629;&#3609;&#3649;&#3585;&#3656;&#3609;)',
			'TH-81' => 'Krabi (&#3585;&#3619;&#3632;&#3610;&#3637;&#3656;)',
			'TH-52' => 'Lampang (&#3621;&#3635;&#3611;&#3634;&#3591;)',
			'TH-51' => 'Lamphun (&#3621;&#3635;&#3614;&#3641;&#3609;)',
			'TH-42' => 'Loei (&#3648;&#3621;&#3618;)',
			'TH-16' => 'Lopburi (&#3621;&#3614;&#3610;&#3640;&#3619;&#3637;)',
			'TH-58' => 'Mae Hong Son (&#3649;&#3617;&#3656;&#3630;&#3656;&#3629;&#3591;&#3626;&#3629;&#3609;)',
			'TH-44' => 'Maha Sarakham (&#3617;&#3627;&#3634;&#3626;&#3634;&#3619;&#3588;&#3634;&#3617;)',
			'TH-49' => 'Mukdahan (&#3617;&#3640;&#3585;&#3604;&#3634;&#3627;&#3634;&#3619;)',
			'TH-26' => 'Nakhon Nayok (&#3609;&#3588;&#3619;&#3609;&#3634;&#3618;&#3585;)',
			'TH-73' => 'Nakhon Pathom (&#3609;&#3588;&#3619;&#3611;&#3600;&#3617;)',
			'TH-48' => 'Nakhon Phanom (&#3609;&#3588;&#3619;&#3614;&#3609;&#3617;)',
			'TH-30' => 'Nakhon Ratchasima (&#3609;&#3588;&#3619;&#3619;&#3634;&#3594;&#3626;&#3637;&#3617;&#3634;)',
			'TH-60' => 'Nakhon Sawan (&#3609;&#3588;&#3619;&#3626;&#3623;&#3619;&#3619;&#3588;&#3660;)',
			'TH-80' => 'Nakhon Si Thammarat (&#3609;&#3588;&#3619;&#3624;&#3619;&#3637;&#3608;&#3619;&#3619;&#3617;&#3619;&#3634;&#3594;)',
			'TH-55' => 'Nan (&#3609;&#3656;&#3634;&#3609;)',
			'TH-96' => 'Narathiwat (&#3609;&#3619;&#3634;&#3608;&#3636;&#3623;&#3634;&#3626;)',
			'TH-39' => 'Nong Bua Lam Phu (&#3627;&#3609;&#3629;&#3591;&#3610;&#3633;&#3623;&#3621;&#3635;&#3616;&#3641;)',
			'TH-43' => 'Nong Khai (&#3627;&#3609;&#3629;&#3591;&#3588;&#3634;&#3618;)',
			'TH-12' => 'Nonthaburi (&#3609;&#3609;&#3607;&#3610;&#3640;&#3619;&#3637;)',
			'TH-13' => 'Pathum Thani (&#3611;&#3607;&#3640;&#3617;&#3608;&#3634;&#3609;&#3637;)',
			'TH-94' => 'Pattani (&#3611;&#3633;&#3605;&#3605;&#3634;&#3609;&#3637;)',
			'TH-82' => 'Phang Nga (&#3614;&#3633;&#3591;&#3591;&#3634;)',
			'TH-93' => 'Phatthalung (&#3614;&#3633;&#3607;&#3621;&#3640;&#3591;)',
			'TH-56' => 'Phayao (&#3614;&#3632;&#3648;&#3618;&#3634;)',
			'TH-67' => 'Phetchabun (&#3648;&#3614;&#3594;&#3619;&#3610;&#3641;&#3619;&#3603;&#3660;)',
			'TH-76' => 'Phetchaburi (&#3648;&#3614;&#3594;&#3619;&#3610;&#3640;&#3619;&#3637;)',
			'TH-66' => 'Phichit (&#3614;&#3636;&#3592;&#3636;&#3605;&#3619;)',
			'TH-65' => 'Phitsanulok (&#3614;&#3636;&#3625;&#3603;&#3640;&#3650;&#3621;&#3585;)',
			'TH-54' => 'Phrae (&#3649;&#3614;&#3619;&#3656;)',
			'TH-83' => 'Phuket (&#3616;&#3641;&#3648;&#3585;&#3655;&#3605;)',
			'TH-25' => 'Prachin Buri (&#3611;&#3619;&#3634;&#3592;&#3637;&#3609;&#3610;&#3640;&#3619;&#3637;)',
			'TH-77' => 'Prachuap Khiri Khan (&#3611;&#3619;&#3632;&#3592;&#3623;&#3610;&#3588;&#3637;&#3619;&#3637;&#3586;&#3633;&#3609;&#3608;&#3660;)',
			'TH-85' => 'Ranong (&#3619;&#3632;&#3609;&#3629;&#3591;)',
			'TH-70' => 'Ratchaburi (&#3619;&#3634;&#3594;&#3610;&#3640;&#3619;&#3637;)',
			'TH-21' => 'Rayong (&#3619;&#3632;&#3618;&#3629;&#3591;)',
			'TH-45' => 'Roi Et (&#3619;&#3657;&#3629;&#3618;&#3648;&#3629;&#3655;&#3604;)',
			'TH-27' => 'Sa Kaeo (&#3626;&#3619;&#3632;&#3649;&#3585;&#3657;&#3623;)',
			'TH-47' => 'Sakon Nakhon (&#3626;&#3585;&#3621;&#3609;&#3588;&#3619;)',
			'TH-11' => 'Samut Prakan (&#3626;&#3617;&#3640;&#3607;&#3619;&#3611;&#3619;&#3634;&#3585;&#3634;&#3619;)',
			'TH-74' => 'Samut Sakhon (&#3626;&#3617;&#3640;&#3607;&#3619;&#3626;&#3634;&#3588;&#3619;)',
			'TH-75' => 'Samut Songkhram (&#3626;&#3617;&#3640;&#3607;&#3619;&#3626;&#3591;&#3588;&#3619;&#3634;&#3617;)',
			'TH-19' => 'Saraburi (&#3626;&#3619;&#3632;&#3610;&#3640;&#3619;&#3637;)',
			'TH-91' => 'Satun (&#3626;&#3605;&#3641;&#3621;)',
			'TH-17' => 'Sing Buri (&#3626;&#3636;&#3591;&#3627;&#3660;&#3610;&#3640;&#3619;&#3637;)',
			'TH-33' => 'Sisaket (&#3624;&#3619;&#3637;&#3626;&#3632;&#3648;&#3585;&#3625;)',
			'TH-90' => 'Songkhla (&#3626;&#3591;&#3586;&#3621;&#3634;)',
			'TH-64' => 'Sukhothai (&#3626;&#3640;&#3650;&#3586;&#3607;&#3633;&#3618;)',
			'TH-72' => 'Suphan Buri (&#3626;&#3640;&#3614;&#3619;&#3619;&#3603;&#3610;&#3640;&#3619;&#3637;)',
			'TH-84' => 'Surat Thani (&#3626;&#3640;&#3619;&#3634;&#3625;&#3598;&#3619;&#3660;&#3608;&#3634;&#3609;&#3637;)',
			'TH-32' => 'Surin (&#3626;&#3640;&#3619;&#3636;&#3609;&#3607;&#3619;&#3660;)',
			'TH-63' => 'Tak (&#3605;&#3634;&#3585;)',
			'TH-92' => 'Trang (&#3605;&#3619;&#3633;&#3591;)',
			'TH-23' => 'Trat (&#3605;&#3619;&#3634;&#3604;)',
			'TH-34' => 'Ubon Ratchathani (&#3629;&#3640;&#3610;&#3621;&#3619;&#3634;&#3594;&#3608;&#3634;&#3609;&#3637;)',
			'TH-41' => 'Udon Thani (&#3629;&#3640;&#3604;&#3619;&#3608;&#3634;&#3609;&#3637;)',
			'TH-61' => 'Uthai Thani (&#3629;&#3640;&#3607;&#3633;&#3618;&#3608;&#3634;&#3609;&#3637;)',
			'TH-53' => 'Uttaradit (&#3629;&#3640;&#3605;&#3619;&#3604;&#3636;&#3605;&#3606;&#3660;)',
			'TH-95' => 'Yala (&#3618;&#3632;&#3621;&#3634;)',
			'TH-35' => 'Yasothon (&#3618;&#3650;&#3626;&#3608;&#3619;)',
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_thailand_states', $states );
	}

	/**
	 * Get Spain States
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of states
	 */
	public function get_spain_list() {
		$states = [
			''   => '',
			'C'  => esc_html__( 'A Coru&ntilde;a', 'event-tickets' ),
			'VI' => esc_html__( 'Álava', 'event-tickets' ),
			'AB' => esc_html__( 'Albacete', 'event-tickets' ),
			'A'  => esc_html__( 'Alicante', 'event-tickets' ),
			'AL' => esc_html__( 'Almer&iacute;a', 'event-tickets' ),
			'O'  => esc_html__( 'Asturias', 'event-tickets' ),
			'AV' => esc_html__( '&Aacute;vila', 'event-tickets' ),
			'BA' => esc_html__( 'Badajoz', 'event-tickets' ),
			'PM' => esc_html__( 'Baleares', 'event-tickets' ),
			'B'  => esc_html__( 'Barcelona', 'event-tickets' ),
			'BU' => esc_html__( 'Burgos', 'event-tickets' ),
			'CC' => esc_html__( 'C&aacute;ceres', 'event-tickets' ),
			'CA' => esc_html__( 'C&aacute;diz', 'event-tickets' ),
			'S'  => esc_html__( 'Cantabria', 'event-tickets' ),
			'CS' => esc_html__( 'Castell&oacute;n', 'event-tickets' ),
			'CE' => esc_html__( 'Ceuta', 'event-tickets' ),
			'CR' => esc_html__( 'Ciudad Real', 'event-tickets' ),
			'CO' => esc_html__( 'C&oacute;rdoba', 'event-tickets' ),
			'CU' => esc_html__( 'Cuenca', 'event-tickets' ),
			'GI' => esc_html__( 'Girona', 'event-tickets' ),
			'GR' => esc_html__( 'Granada', 'event-tickets' ),
			'GU' => esc_html__( 'Guadalajara', 'event-tickets' ),
			'SS' => esc_html__( 'Gipuzkoa', 'event-tickets' ),
			'H'  => esc_html__( 'Huelva', 'event-tickets' ),
			'HU' => esc_html__( 'Huesca', 'event-tickets' ),
			'J'  => esc_html__( 'Ja&eacute;n', 'event-tickets' ),
			'LO' => esc_html__( 'La Rioja', 'event-tickets' ),
			'GC' => esc_html__( 'Las Palmas', 'event-tickets' ),
			'LE' => esc_html__( 'Le&oacute;n', 'event-tickets' ),
			'L'  => esc_html__( 'Lleida', 'event-tickets' ),
			'LU' => esc_html__( 'Lugo', 'event-tickets' ),
			'M'  => esc_html__( 'Madrid', 'event-tickets' ),
			'MA' => esc_html__( 'M&aacute;laga', 'event-tickets' ),
			'ML' => esc_html__( 'Melilla', 'event-tickets' ),
			'MU' => esc_html__( 'Murcia', 'event-tickets' ),
			'NA' => esc_html__( 'Navarra', 'event-tickets' ),
			'OR' => esc_html__( 'Ourense', 'event-tickets' ),
			'P'  => esc_html__( 'Palencia', 'event-tickets' ),
			'PO' => esc_html__( 'Pontevedra', 'event-tickets' ),
			'SA' => esc_html__( 'Salamanca', 'event-tickets' ),
			'TF' => esc_html__( 'Santa Cruz de Tenerife', 'event-tickets' ),
			'SG' => esc_html__( 'Segovia', 'event-tickets' ),
			'SE' => esc_html__( 'Sevilla', 'event-tickets' ),
			'SO' => esc_html__( 'Soria', 'event-tickets' ),
			'T'  => esc_html__( 'Tarragona', 'event-tickets' ),
			'TE' => esc_html__( 'Teruel', 'event-tickets' ),
			'TO' => esc_html__( 'Toledo', 'event-tickets' ),
			'V'  => esc_html__( 'Valencia', 'event-tickets' ),
			'VA' => esc_html__( 'Valladolid', 'event-tickets' ),
			'BI' => esc_html__( 'Bizkaia', 'event-tickets' ),
			'ZA' => esc_html__( 'Zamora', 'event-tickets' ),
			'Z'  => esc_html__( 'Zaragoza', 'event-tickets' ),
		];

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_states_spain_states', $states );
	}


	/**
	 * Get the label that need to show as an placeholder.
	 *
	 * @since 5.2.0
	 *
	 * @return array $country_states_label
	 */
	public function get_states_label() {
		$country_states_label = [];
		$default_label        = __( 'State', 'event-tickets' );
		$locale               = tribe( \TEC\Tickets\Commerce\Gateways\PayPal\Location\Country::class )->get_list_locale();
		foreach ( $locale as $key => $value ) {
			$label = $default_label;
			if ( ! empty( $value['state'] ) && ! empty( $value['state']['label'] ) ) {
				$label = $value['state']['label'];
			}
			$country_states_label[ $key ] = $label;
		}

		/**
		 * Filter can be used to add or remove the Country that does not have states init.
		 *
		 * @since 5.2.0
		 *
		 * @param array $country Contain key as there country code & value as there country name.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_states_get_states_label', $country_states_label );
	}

	/**
	 * Get Site Base State
	 *
	 * @since 5.2.0
	 *
	 * @return string $state The site's base state name
	 */
	public function get_state() {
		$state = tribe_get_option( 'tec-tickets-commerce-gateway-paypal-merchant-state' );

		return apply_filters( 'tec_tickets_commerce_gateway_paypal_state', $state );
	}

	/**
	 * Get Site States
	 *
	 * @since 5.2.0
	 *
	 * @param null $country
	 *
	 * @return mixed  A list of states for the site's base country.
	 */
	public function get_states_for( $country = null ) {
		// If Country have no states return empty array.
		$states = [];

		// Check if Country Code is empty or not.
		if ( empty( $country ) ) {
			// Get default country code that is being set by the admin.
			$country = tribe( \TEC\Tickets\Commerce\Gateways\PayPal\Location\Country::class )->get_setting();
		}

		// Get all the list of the states in array key format where key is the country code and value is the states that it contain.
		$states_list = $this->get_list();

		// Check if $country code exists in the array key.
		if ( array_key_exists( $country, $states_list ) ) {
			$states = $states_list[ $country ];
		}

		/**
		 * Filter the query in case tables are non-standard.
		 *
		 * @since 5.2.0
		 *
		 * @param string $query Database count query
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_states_for_country', $states );
	}

	/**
	 * Get States List.
	 *
	 * @since 5.2.0
	 *
	 * @return array $states A list of the available states as in array key format.
	 */
	public function get_list() {
		$states = [
			'US' => $this->get_us_list(),
			'CA' => $this->get_provinces_list(),
			'AU' => $this->get_australian_list(),
			'BR' => $this->get_brazil_list(),
			'CN' => $this->get_chinese_list(),
			'HK' => $this->get_hong_kong_list(),
			'HU' => $this->get_hungary_list(),
			'ID' => $this->get_indonesian_list(),
			'IN' => $this->get_indian_list(),
			'MY' => $this->get_malaysian_list(),
			'NZ' => $this->get_new_zealand_list(),
			'TH' => $this->get_thailand_list(),
			'ZA' => $this->get_south_african_list(),
			'ES' => $this->get_spain_list(),
			'TR' => $this->get_turkey_list(),
			'RO' => $this->get_romania_list(),
			'PK' => $this->get_pakistan_list(),
			'PH' => $this->get_philippines_list(),
			'PE' => $this->get_peru_list(),
			'NP' => $this->get_nepal_list(),
			'NG' => $this->get_nigerian_list(),
			'MX' => $this->get_mexico_list(),
			'JP' => $this->get_japan_list(),
			'IT' => $this->get_italy_list(),
			'IR' => $this->get_iran_list(),
			'IE' => $this->get_ireland_list(),
			'GR' => $this->get_greek_list(),
			'BO' => $this->get_bolivian_list(),
			'BG' => $this->get_bulgarian_list(),
			'BD' => $this->get_bangladeshi_list(),
			'AR' => $this->get_argentina_list(),
		];

		/**
		 * Filter can be used to add or remove the States from the Country.
		 *
		 * Filters can be use to add states inside the country all the states will be in array format ans the array key will be country code.
		 *
		 * @since 5.2.0
		 *
		 * @param array $states Contain the list of states in array key format where key of the array is there respected country code.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_list', $states );
	}
}
