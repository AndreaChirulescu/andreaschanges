<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if (!function_exists('giglogadmin_populate_countries')) {
    function giglogadmin_populate_countries()
    {
        global $wpdb;
        $wpdb->query(
            "INSERT INTO `wpg_countries` (`id`, `wpgc_fullname`, `wpgcountry_name`, `wpgc_iso3`, `wpgc_numcode`) VALUES
                ('AD', 'ANDORRA', 'Andorra', 'AND', '020'),
                ('AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', '784'),
                ('AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', '004'),
                ('AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', '028'),
                ('AI', 'ANGUILLA', 'Anguilla', 'AIA', '660'),
                ('AL', 'ALBANIA', 'Albania', 'ALB', '008'),
                ('AM', 'ARMENIA', 'Armenia', 'ARM', '051'),
                ('AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', '530'),
                ('AO', 'ANGOLA', 'Angola', 'AGO', '024'),
                ('AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL),
                ('AR', 'ARGENTINA', 'Argentina', 'ARG', '032'),
                ('AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', '016'),
                ('AT', 'AUSTRIA', 'Austria', 'AUT', '040'),
                ('AU', 'AUSTRALIA', 'Australia', 'AUS', '036'),
                ('AW', 'ARUBA', 'Aruba', 'ABW', '533'),
                ('AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', '031'),
                ('BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', '070'),
                ('BB', 'BARBADOS', 'Barbados', 'BRB', '052'),
                ('BD', 'BANGLADESH', 'Bangladesh', 'BGD', '050'),
                ('BE', 'BELGIUM', 'Belgium', 'BEL', '056'),
                ('BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', '854'),
                ('BG', 'BULGARIA', 'Bulgaria', 'BGR', '100'),
                ('BH', 'BAHRAIN', 'Bahrain', 'BHR', '048'),
                ('BI', 'BURUNDI', 'Burundi', 'BDI', '108'),
                ('BJ', 'BENIN', 'Benin', 'BEN', '204'),
                ('BM', 'BERMUDA', 'Bermuda', 'BMU', '060'),
                ('BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', '096'),
                ('BO', 'BOLIVIA', 'Bolivia', 'BOL', '068'),
                ('BR', 'BRAZIL', 'Brazil', 'BRA', '076'),
                ('BS', 'BAHAMAS', 'Bahamas', 'BHS', '044'),
                ('BT', 'BHUTAN', 'Bhutan', 'BTN', '064'),
                ('BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL),
                ('BW', 'BOTSWANA', 'Botswana', 'BWA', '072'),
                ('BY', 'BELARUS', 'Belarus', 'BLR', '112'),
                ('BZ', 'BELIZE', 'Belize', 'BLZ', '084'),
                ('CA', 'CANADA', 'Canada', 'CAN', '124'),
                ('CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL),
                ('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', '180'),
                ('CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', '140'),
                ('CG', 'CONGO', 'Congo', 'COG', '178'),
                ('CH', 'SWITZERLAND', 'Switzerland', 'CHE', '756'),
                ('CI', 'COTE D\'IVOIRE', 'Cote D\'Ivoire', 'CIV', '384'),
                ('CK', 'COOK ISLANDS', 'Cook Islands', 'COK', '184'),
                ('CL', 'CHILE', 'Chile', 'CHL', '152'),
                ('CM', 'CAMEROON', 'Cameroon', 'CMR', '120'),
                ('CN', 'CHINA', 'China', 'CHN', '156'),
                ('CO', 'COLOMBIA', 'Colombia', 'COL', '170'),
                ('CR', 'COSTA RICA', 'Costa Rica', 'CRI', '188'),
                ('CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL),
                ('CU', 'CUBA', 'Cuba', 'CUB', '192'),
                ('CV', 'CAPE VERDE', 'Cape Verde', 'CPV', '132'),
                ('CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL),
                ('CY', 'CYPRUS', 'Cyprus', 'CYP', '196'),
                ('CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', '203'),
                ('DE', 'GERMANY', 'Germany', 'DEU', '276'),
                ('DJ', 'DJIBOUTI', 'Djibouti', 'DJI', '262'),
                ('DK', 'DENMARK', 'Denmark', 'DNK', '208'),
                ('DM', 'DOMINICA', 'Dominica', 'DMA', '212'),
                ('DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', '214'),
                ('DZ', 'ALGERIA', 'Algeria', 'DZA', '012'),
                ('EC', 'ECUADOR', 'Ecuador', 'ECU', '218'),
                ('EE', 'ESTONIA', 'Estonia', 'EST', '233'),
                ('EG', 'EGYPT', 'Egypt', 'EGY', '818'),
                ('EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', '732'),
                ('ER', 'ERITREA', 'Eritrea', 'ERI', '232'),
                ('ES', 'SPAIN', 'Spain', 'ESP', '724'),
                ('ET', 'ETHIOPIA', 'Ethiopia', 'ETH', '231'),
                ('FI', 'FINLAND', 'Finland', 'FIN', '246'),
                ('FJ', 'FIJI', 'Fiji', 'FJI', '242'),
                ('FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', '238'),
                ('FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', '583'),
                ('FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', '234'),
                ('FR', 'FRANCE', 'France', 'FRA', '250'),
                ('GA', 'GABON', 'Gabon', 'GAB', '266'),
                ('GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', '826'),
                ('GD', 'GRENADA', 'Grenada', 'GRD', '308'),
                ('GE', 'GEORGIA', 'Georgia', 'GEO', '268'),
                ('GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', '254'),
                ('GH', 'GHANA', 'Ghana', 'GHA', '288'),
                ('GI', 'GIBRALTAR', 'Gibraltar', 'GIB', '292'),
                ('GL', 'GREENLAND', 'Greenland', 'GRL', '304'),
                ('GM', 'GAMBIA', 'Gambia', 'GMB', '270'),
                ('GN', 'GUINEA', 'Guinea', 'GIN', '324'),
                ('GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', '312'),
                ('GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', '226'),
                ('GR', 'GREECE', 'Greece', 'GRC', '300'),
                ('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL),
                ('GT', 'GUATEMALA', 'Guatemala', 'GTM', '320'),
                ('GU', 'GUAM', 'Guam', 'GUM', '316'),
                ('GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', '624'),
                ('GY', 'GUYANA', 'Guyana', 'GUY', '328'),
                ('HK', 'HONG KONG', 'Hong Kong', 'HKG', '344'),
                ('HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL),
                ('HN', 'HONDURAS', 'Honduras', 'HND', '340'),
                ('HR', 'CROATIA', 'Croatia', 'HRV', '191'),
                ('HT', 'HAITI', 'Haiti', 'HTI', '332'),
                ('HU', 'HUNGARY', 'Hungary', 'HUN', '348'),
                ('ID', 'INDONESIA', 'Indonesia', 'IDN', '360'),
                ('IE', 'IRELAND', 'Ireland', 'IRL', '372'),
                ('IL', 'ISRAEL', 'Israel', 'ISR', '376'),
                ('IN', 'INDIA', 'India', 'IND', '356'),
                ('IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL),
                ('IQ', 'IRAQ', 'Iraq', 'IRQ', '368'),
                ('IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', '364'),
                ('IS', 'ICELAND', 'Iceland', 'ISL', '352'),
                ('IT', 'ITALY', 'Italy', 'ITA', '380'),
                ('JM', 'JAMAICA', 'Jamaica', 'JAM', '388'),
                ('JO', 'JORDAN', 'Jordan', 'JOR', '400'),
                ('JP', 'JAPAN', 'Japan', 'JPN', '392'),
                ('KE', 'KENYA', 'Kenya', 'KEN', '404'),
                ('KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', '417'),
                ('KH', 'CAMBODIA', 'Cambodia', 'KHM', '116'),
                ('KI', 'KIRIBATI', 'Kiribati', 'KIR', '296'),
                ('KM', 'COMOROS', 'Comoros', 'COM', '174'),
                ('KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', '659'),
                ('KP', 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'Korea, Democratic People\'s Republic of', 'PRK', '408'),
                ('KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', '410'),
                ('KW', 'KUWAIT', 'Kuwait', 'KWT', '414'),
                ('KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', '136'),
                ('KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', '398'),
                ('LA', 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'Lao People\'s Democratic Republic', 'LAO', '418'),
                ('LB', 'LEBANON', 'Lebanon', 'LBN', '422'),
                ('LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', '662'),
                ('LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', '438'),
                ('LK', 'SRI LANKA', 'Sri Lanka', 'LKA', '144'),
                ('LR', 'LIBERIA', 'Liberia', 'LBR', '430'),
                ('LS', 'LESOTHO', 'Lesotho', 'LSO', '426'),
                ('LT', 'LITHUANIA', 'Lithuania', 'LTU', '440'),
                ('LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', '442'),
                ('LV', 'LATVIA', 'Latvia', 'LVA', '428'),
                ('LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', '434'),
                ('MA', 'MOROCCO', 'Morocco', 'MAR', '504'),
                ('MC', 'MONACO', 'Monaco', 'MCO', '492'),
                ('MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', '498'),
                ('MG', 'MADAGASCAR', 'Madagascar', 'MDG', '450'),
                ('MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', '584'),
                ('MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', '807'),
                ('ML', 'MALI', 'Mali', 'MLI', '466'),
                ('MM', 'MYANMAR', 'Myanmar', 'MMR', '104'),
                ('MN', 'MONGOLIA', 'Mongolia', 'MNG', '496'),
                ('MO', 'MACAO', 'Macao', 'MAC', '446'),
                ('MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', '580'),
                ('MQ', 'MARTINIQUE', 'Martinique', 'MTQ', '474'),
                ('MR', 'MAURITANIA', 'Mauritania', 'MRT', '478'),
                ('MS', 'MONTSERRAT', 'Montserrat', 'MSR', '500'),
                ('MT', 'MALTA', 'Malta', 'MLT', '470'),
                ('MU', 'MAURITIUS', 'Mauritius', 'MUS', '480'),
                ('MV', 'MALDIVES', 'Maldives', 'MDV', '462'),
                ('MW', 'MALAWI', 'Malawi', 'MWI', '454'),
                ('MX', 'MEXICO', 'Mexico', 'MEX', '484'),
                ('MY', 'MALAYSIA', 'Malaysia', 'MYS', '458'),
                ('MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', '508'),
                ('NA', 'NAMIBIA', 'Namibia', 'NAM', '516'),
                ('NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', '540'),
                ('NE', 'NIGER', 'Niger', 'NER', '562'),
                ('NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', '574'),
                ('NG', 'NIGERIA', 'Nigeria', 'NGA', '566'),
                ('NI', 'NICARAGUA', 'Nicaragua', 'NIC', '558'),
                ('NL', 'NETHERLANDS', 'Netherlands', 'NLD', '528'),
                ('NO', 'NORWAY', 'Norway', 'NOR', '578'),
                ('NP', 'NEPAL', 'Nepal', 'NPL', '524'),
                ('NR', 'NAURU', 'Nauru', 'NRU', '520'),
                ('NU', 'NIUE', 'Niue', 'NIU', '570'),
                ('NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', '554'),
                ('OM', 'OMAN', 'Oman', 'OMN', '512'),
                ('PA', 'PANAMA', 'Panama', 'PAN', '591'),
                ('PE', 'PERU', 'Peru', 'PER', '604'),
                ('PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', '258'),
                ('PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', '598'),
                ('PH', 'PHILIPPINES', 'Philippines', 'PHL', '608'),
                ('PK', 'PAKISTAN', 'Pakistan', 'PAK', '586'),
                ('PL', 'POLAND', 'Poland', 'POL', '616'),
                ('PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', '666'),
                ('PN', 'PITCAIRN', 'Pitcairn', 'PCN', '612'),
                ('PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', '630'),
                ('PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL),
                ('PT', 'PORTUGAL', 'Portugal', 'PRT', '620'),
                ('PW', 'PALAU', 'Palau', 'PLW', '585'),
                ('PY', 'PARAGUAY', 'Paraguay', 'PRY', '600'),
                ('QA', 'QATAR', 'Qatar', 'QAT', '634'),
                ('RE', 'REUNION', 'Reunion', 'REU', '638'),
                ('RO', 'ROMANIA', 'Romania', 'ROM', '642'),
                ('RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', '643'),
                ('RW', 'RWANDA', 'Rwanda', 'RWA', '646'),
                ('SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', '682'),
                ('SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', '090'),
                ('SC', 'SEYCHELLES', 'Seychelles', 'SYC', '690'),
                ('SD', 'SUDAN', 'Sudan', 'SDN', '736'),
                ('SE', 'SWEDEN', 'Sweden', 'SWE', '752'),
                ('SG', 'SINGAPORE', 'Singapore', 'SGP', '702'),
                ('SH', 'SAINT HELENA', 'Saint Helena', 'SHN', '654'),
                ('SI', 'SLOVENIA', 'Slovenia', 'SVN', '705'),
                ('SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', '744'),
                ('SK', 'SLOVAKIA', 'Slovakia', 'SVK', '703'),
                ('SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', '694'),
                ('SM', 'SAN MARINO', 'San Marino', 'SMR', '674'),
                ('SN', 'SENEGAL', 'Senegal', 'SEN', '686'),
                ('SO', 'SOMALIA', 'Somalia', 'SOM', '706'),
                ('SR', 'SURINAME', 'Suriname', 'SUR', '740'),
                ('ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', '678'),
                ('SV', 'EL SALVADOR', 'El Salvador', 'SLV', '222'),
                ('SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', '760'),
                ('SZ', 'SWAZILAND', 'Swaziland', 'SWZ', '748'),
                ('TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', '796'),
                ('TD', 'CHAD', 'Chad', 'TCD', '148'),
                ('TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL),
                ('TG', 'TOGO', 'Togo', 'TGO', '768'),
                ('TH', 'THAILAND', 'Thailand', 'THA', '764'),
                ('TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', '762'),
                ('TK', 'TOKELAU', 'Tokelau', 'TKL', '772'),
                ('TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL),
                ('TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', '795'),
                ('TN', 'TUNISIA', 'Tunisia', 'TUN', '788'),
                ('TO', 'TONGA', 'Tonga', 'TON', '776'),
                ('TR', 'TURKEY', 'Turkey', 'TUR', '792'),
                ('TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', '780'),
                ('TV', 'TUVALU', 'Tuvalu', 'TUV', '798'),
                ('TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', '158'),
                ('TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', '834'),
                ('UA', 'UKRAINE', 'Ukraine', 'UKR', '804'),
                ('UG', 'UGANDA', 'Uganda', 'UGA', '800'),
                ('UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL),
                ('US', 'UNITED STATES', 'United States', 'USA', '840'),
                ('UY', 'URUGUAY', 'Uruguay', 'URY', '858'),
                ('UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', '860'),
                ('VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', '336'),
                ('VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', '670'),
                ('VE', 'VENEZUELA', 'Venezuela', 'VEN', '862'),
                ('VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', '092'),
                ('VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', '850'),
                ('VN', 'VIET NAM', 'Viet Nam', 'VNM', '704'),
                ('VU', 'VANUATU', 'Vanuatu', 'VUT', '548'),
                ('WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', '876'),
                ('WS', 'SAMOA', 'Samoa', 'WSM', '882'),
                ('YE', 'YEMEN', 'Yemen', 'YEM', '887'),
                ('YT', 'MAYOTTE', 'Mayotte', NULL, NULL),
                ('ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', '710'),
                ('ZM', 'ZAMBIA', 'Zambia', 'ZMB', '894'),
                ('ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', '716');");
    }
}

if ( !function_exists( "giglog_register_db_tables") )
{
    function giglog_register_db_tables()
    {
        $db_version = get_option('giglogadmin_db_version');
        if ($db_version == 5) {
            return;
        }

        $bands_table =
            "CREATE TABLE IF NOT EXISTS `wpg_bands` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wpgband_name` varchar(500) NOT NULL,
                `wpgband_country` varchar(4) DEFAULT 'NO',
                PRIMARY KEY (`id`),
                KEY `wpgband_country` (`wpgband_country`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $concertlogs_table =
            "CREATE TABLE IF NOT EXISTS `wpg_concertlogs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wpgcl_concertid` int(11) NOT NULL,
                `wpgcl_photo1` varchar(200) DEFAULT NULL,
                `wpgcl_photo2` varchar(200) DEFAULT NULL,
                `wpgcl_rev1` varchar(200) DEFAULT NULL,
                `wpgcl_rev2` varchar(200) DEFAULT NULL,
                `wpgcl_int` varchar(200) DEFAULT NULL,
                `wpgcl_status` int(11) DEFAULT 1,
                PRIMARY KEY (`id`),
                KEY `wpglog_status` (`wpgcl_status`),
                KEY `wpglog_concerts` (`wpgcl_concertid`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $concerts_table =
            "CREATE TABLE IF NOT EXISTS `wpg_concerts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `band` int(11) NOT NULL,
                `venue` int(11) NOT NULL,
                `wpgconcert_date` date NOT NULL DEFAULT current_timestamp(),
                `wpgconcert_tickets` varchar(2000) DEFAULT NULL,
                `wpgconcert_event` varchar(2000) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `wpgconcert_band` (`band`),
                KEY `wpgconcert_venue` (`venue`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $countries_table =
            "CREATE TABLE IF NOT EXISTS `wpg_countries` (
                `id` varchar(4) NOT NULL,
                `wpgc_fullname` varchar(200) NOT NULL,
                `wpgcountry_name` varchar(500) NOT NULL,
                `wpgc_iso3` varchar(3) DEFAULT NULL,
                `wpgc_numcode` varchar(5) DEFAULT NULL,
                PRIMARY KEY (`id`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $files_table =
            "CREATE TABLE IF NOT EXISTS `wpg_files` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `filename` varchar(2000) NOT NULL,
                `filedate` datetime NOT NULL DEFAULT current_timestamp(),
                `rowid` int(11) NOT NULL,
                `rowcontent` text NOT NULL,
                `processed` char(1) NOT NULL DEFAULT 'N',
                `wpgc_id` int(11) NOT NULL,
                PRIMARY KEY (`id`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $logchanges_table =
            "CREATE TABLE IF NOT EXISTS `wpg_logchanges` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `userid` varchar(500) NOT NULL,
                `action` varchar(500) NOT NULL,
                `actiondate` date NOT NULL DEFAULT current_timestamp(),
                `concertid` int(11) NOT NULL,
                PRIMARY KEY (`id`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $pressstatus_table =
            "CREATE TABLE IF NOT EXISTS `wpg_pressstatus` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wpgs_name` varchar(50) NOT NULL,
                PRIMARY KEY (`id`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $venues_table =
            "CREATE TABLE IF NOT EXISTS `wpg_venues` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wpgvenue_name` varchar(500) NOT NULL,
                `wpgvenue_city` varchar(250) DEFAULT NULL,
                `wpgvenue_address` varchar(2000) DEFAULT NULL,
                `wpgvenue_webpage` varchar(200) DEFAULT NULL,
                PRIMARY KEY (`id`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        global $wpdb;
        $tables = array(
            $bands_table,
            $concertlogs_table,
            $concerts_table,
            $countries_table,
            $files_table,
            $logchanges_table,
            $pressstatus_table,
            $venues_table);

        foreach($tables as $tabledef) {
            $result = $wpdb->query($tabledef);
            if ($result === false) {
                error_log("Registering table failed.");
            }
        }

        if ($db_version == NULL || $db_version < 1)
        {
            giglogadmin_populate_countries();

            $wpdb->query(
                "ALTER TABLE `wpg_countries`
                 ADD FULLTEXT KEY `id`
                    (`id`,`wpgc_fullname`,`wpgcountry_name`,`wpgc_iso3`,`wpgc_numcode`);");

            $wpdb->query(
                "ALTER TABLE `wpg_bands`
                    ADD CONSTRAINT `wpgband_country`
                        FOREIGN KEY (`wpgband_country`)
                        REFERENCES `wpg_countries` (`id`) ON DELETE NO ACTION;");

            $wpdb->query(
                "ALTER TABLE `wpg_concertlogs`
                    ADD CONSTRAINT `wpglog_concerts`
                        FOREIGN KEY (`wpgcl_concertid`)
                        REFERENCES `wpg_concerts` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
                    ADD CONSTRAINT `wpglog_status`
                        FOREIGN KEY (`wpgcl_status`)
                        REFERENCES `wpg_pressstatus` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");

            $wpdb->query(
                "ALTER TABLE `wpg_concerts`
                    ADD CONSTRAINT `wpgconcert_band`
                        FOREIGN KEY (`band`)
                        REFERENCES `wpg_bands` (`id`) ON DELETE NO ACTION,
                    ADD CONSTRAINT `wpgconcert_venue`
                        FOREIGN KEY (`venue`)
                        REFERENCES `wpg_venues` (`id`) ON DELETE NO ACTION;");
        }

        if ($db_version == NULL || $db_version < 2)
        {
            $wpdb->query(
                "INSERT INTO `wpg_pressstatus` (`id`, `wpgs_name`) VALUES
                    (1, ' '),
                    (2, 'Accred Requested'),
                    (3, 'Photo Approved'),
                    (4, 'Text Approved'),
                    (5, 'Photo and Text approved'),
                    (6, 'Rejected');");
        }

        if ($db_version == NULL || $db_version < 3)
        {
            $wpdb->query(
                "ALTER TABLE `wpg_concertlogs`
                    ADD COLUMN IF NOT EXISTS
                        `wpgcl_createddate` date NOT NULL DEFAULT current_timestamp();");
        }

        if ($db_version == NULL || $db_version < 4)
        {
            $wpdb->query(
                "INSERT INTO `wpg_countries` (`id`, `wpgc_fullname`, `wpgcountry_name`, `wpgc_iso3`, `wpgc_numcode`) VALUES ('NN', 'UNKNOWN', 'Unknown', 'NNN', '666');");
        }

        if ($db_version == NULL || $db_version < 5)
        {
            $wpdb->query(
                "ALTER TABLE `wpg_concerts`
                ADD COLUMN IF NOT EXISTS
                `wpgconcert_name` VARCHAR(2000) NOT NULL AFTER `id`;");
            $wpdb->query(
                "ALTER TABLE `wpg_concerts`
                ADD COLUMN IF NOT EXISTS
                `wpgconcert_type` INT NOT NULL DEFAULT '1' COMMENT '1 concert, 2 festival';");
            $wpdb->query(
                "ALTER TABLE `wpg_concerts` DROP INDEX `wpgconcert_band`;");

            $wpdb->query(
                "ALTER TABLE `wpg_concerts` DROP FOREIGN KEY `wpgconcert_band`;");
        }

        update_option("giglogadmin_db_version", 5);
    }

    giglog_register_db_tables();
}

?>
