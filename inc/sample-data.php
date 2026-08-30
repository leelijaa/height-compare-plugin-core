<?php
/**
 * Dev-only sample data seeder (loaded only when WP_DEBUG is true).
 *
 * @package HeightCompare
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sample celebrities keyed by group name.
 * Each item is a named array of meta values.
 *
 * @return array<string, list<array<string,mixed>>>
 */
function hc_sample_celebrities(): array {
	return array(

		'Footballers' => array(
			array(
				'name'           => 'Cristiano Ronaldo',
				'height_cm'      => 187.0,
				'gender'         => 'male',
				'country'        => 'PT',
				'category'       => 'Football',
				'aliases'        => 'CR7',
				'dob'            => '1985-02-05',
				'birthplace'     => 'Funchal, Madeira, Portugal',
				'weight_kg'      => 83,
				'volume'         => 74000,
				'birth_name'     => 'Cristiano Ronaldo dos Santos Aveiro',
				'full_name'      => 'Cristiano Ronaldo dos Santos Aveiro',
				'nickname'       => 'CR7',
				'profession'     => 'Footballer',
				'school'         => 'Escola Básica e Secundária Gonçalves Zarco',
				'college'        => 'N/A',
				'father_name'    => 'José Dinis Aveiro',
				'mother_name'    => 'Maria Dolores dos Santos Viveiros da Aveiro',
				'siblings'       => 'Hugo dos Santos Aveiro, Elma dos Santos Aveiro, Katie Aveiro',
				'marital_status' => 'Married',
				'wife_name'      => 'Georgina Rodríguez',
				'girlfriend'     => 'N/A',
				'friends'        => 'Jose Semedo, Ricky Regufe',
				'religion'       => 'Christian',
				'hometown'       => 'Funchal, Madeira, Portugal',
				'address'        => 'La Finca, Madrid, Spain',
				'children'       => 'Cristiano Ronaldo Jr., Eva Maria dos Santos, Bella Esmeralda, Alana Martina dos Santos Aveiro, Mateo Ronaldo',
				'hobbies'        => 'Workout, Music, Bike Riding',
				'awards'         => 'European Golden Shoe, FIFA World Player of the Year, Goal 50, FIFA Ballon d\'Or, World Soccer Player of the Year, Premiere League Golden Boot, La Liga Player of the Month',
				'net_worth'      => '$1.1 Billion',
				'monthly_earn'   => '$10 Million',
			),
			array(
				'name'           => 'Lionel Messi',
				'height_cm'      => 170.0,
				'gender'         => 'male',
				'country'        => 'AR',
				'category'       => 'Football',
				'aliases'        => 'Leo Messi',
				'dob'            => '1987-06-24',
				'birthplace'     => 'Rosario, Argentina',
				'weight_kg'      => 72,
				'volume'         => 90500,
				'birth_name'     => 'Lionel Andrés Messi Cuccittini',
				'full_name'      => 'Lionel Andrés Messi Cuccittini',
				'nickname'       => 'La Pulga',
				'profession'     => 'Footballer',
				'school'         => 'Las Heras School',
				'college'        => 'N/A',
				'father_name'    => 'Jorge Messi',
				'mother_name'    => 'Celia María Cuccittini',
				'siblings'       => 'Rodrigo Messi, Matías Messi, María Sol Messi',
				'marital_status' => 'Married',
				'wife_name'      => 'Antonela Roccuzzo',
				'girlfriend'     => 'N/A',
				'friends'        => 'Luis Suárez, Cesc Fàbregas',
				'religion'       => 'Christian',
				'hometown'       => 'Rosario, Argentina',
				'address'        => 'Fort Lauderdale, Florida, USA',
				'children'       => 'Thiago Messi, Mateo Messi, Ciro Messi',
				'hobbies'        => 'Football, Gaming, Family Time',
				'awards'         => 'FIFA Ballon d\'Or (8x), FIFA World Cup 2022, Copa América, Champions League',
				'net_worth'      => '$600 Million',
				'monthly_earn'   => '$4 Million',
			),
			array(
				'name'           => 'Neymar Jr',
				'height_cm'      => 175.0,
				'gender'         => 'male',
				'country'        => 'BR',
				'category'       => 'Football',
				'aliases'        => 'Ney',
				'dob'            => '1992-02-05',
				'birthplace'     => 'Mogi das Cruzes, Brazil',
				'weight_kg'      => 68,
				'volume'         => 40500,
				'birth_name'     => 'Neymar da Silva Santos Júnior',
				'full_name'      => 'Neymar da Silva Santos Júnior',
				'nickname'       => 'Ney',
				'profession'     => 'Footballer',
				'school'         => 'Portuguesa Santista',
				'college'        => 'N/A',
				'father_name'    => 'Neymar Santos Sr.',
				'mother_name'    => 'Nadine Gonçalves',
				'siblings'       => 'Rafaella Santos',
				'marital_status' => 'Single',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'Bruna Biancardi',
				'friends'        => 'Dani Alves, Rafinha',
				'religion'       => 'Christian',
				'hometown'       => 'Mogi das Cruzes, Brazil',
				'address'        => 'Riyadh, Saudi Arabia',
				'children'       => 'Davi Lucca da Silva Santos, Mavie',
				'hobbies'        => 'Gaming, Dancing, Poker',
				'awards'         => 'Copa América 2019, Olympic Gold 2016, FIFPro World XI',
				'net_worth'      => '$200 Million',
				'monthly_earn'   => '$2.5 Million',
			),
			array(
				'name'           => 'Kylian Mbappé',
				'height_cm'      => 178.0,
				'gender'         => 'male',
				'country'        => 'FR',
				'category'       => 'Football',
				'aliases'        => 'Kiki',
				'dob'            => '1998-12-20',
				'birthplace'     => 'Paris, France',
				'weight_kg'      => 73,
				'volume'         => 55000,
				'birth_name'     => 'Kylian Adeyemi Mbappé Lottin',
				'full_name'      => 'Kylian Adeyemi Mbappé Lottin',
				'nickname'       => 'Donatello',
				'profession'     => 'Footballer',
				'school'         => 'Clairefontaine Academy',
				'college'        => 'N/A',
				'father_name'    => 'Wilfrid Mbappé',
				'mother_name'    => 'Fayza Lamari',
				'siblings'       => 'Ethan Mbappé, Jirès Kembo Ekoko',
				'marital_status' => 'Single',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Achraf Hakimi, Antoine Griezmann',
				'religion'       => 'Muslim',
				'hometown'       => 'Bondy, Île-de-France, France',
				'address'        => 'Madrid, Spain',
				'children'       => 'N/A',
				'hobbies'        => 'Gaming, Reading, Basketball',
				'awards'         => 'FIFA World Cup 2018, Ligue 1 Player of the Year, Golden Boot FIFA World Cup 2022',
				'net_worth'      => '$150 Million',
				'monthly_earn'   => '$5.5 Million',
			),
			array(
				'name'           => 'Erling Haaland',
				'height_cm'      => 194.0,
				'gender'         => 'male',
				'country'        => 'NO',
				'category'       => 'Football',
				'aliases'        => '',
				'dob'            => '2000-07-21',
				'birthplace'     => 'Leeds, England',
				'weight_kg'      => 88,
				'volume'         => 38000,
				'birth_name'     => 'Erling Braut Haaland',
				'full_name'      => 'Erling Braut Haaland',
				'nickname'       => 'The Terminator',
				'profession'     => 'Footballer',
				'school'         => 'Bryne FK Academy',
				'college'        => 'N/A',
				'father_name'    => 'Alfie Haaland',
				'mother_name'    => 'Gry Marita Braut',
				'siblings'       => 'Astor Haaland',
				'marital_status' => 'Single',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'Isabel Haugseng Johansen',
				'friends'        => 'Sergio Gómez, Julian Alvarez',
				'religion'       => 'Christian',
				'hometown'       => 'Bryne, Norway',
				'address'        => 'Manchester, England',
				'children'       => 'N/A',
				'hobbies'        => 'Sleeping, Training, Fishing',
				'awards'         => 'Bundesliga Top Scorer, Premier League Top Scorer, FWA Footballer of the Year, PFA Players\' Player of the Year',
				'net_worth'      => '$80 Million',
				'monthly_earn'   => '$2 Million',
			),
		),

		'Basketball Players' => array(
			array(
				'name'           => 'LeBron James',
				'height_cm'      => 206.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Basketball',
				'aliases'        => 'King James',
				'dob'            => '1984-12-30',
				'birthplace'     => 'Akron, Ohio, USA',
				'weight_kg'      => 113,
				'volume'         => 40500,
				'birth_name'     => 'LeBron Raymone James Sr.',
				'full_name'      => 'LeBron Raymone James Sr.',
				'nickname'       => 'King James, LBJ, The Chosen One',
				'profession'     => 'Basketball Player',
				'school'         => 'St. Vincent-St. Mary High School',
				'college'        => 'N/A',
				'father_name'    => 'Anthony McClelland',
				'mother_name'    => 'Gloria Marie James',
				'siblings'       => 'N/A',
				'marital_status' => 'Married',
				'wife_name'      => 'Savannah Brinson',
				'girlfriend'     => 'N/A',
				'friends'        => 'Dwyane Wade, Chris Paul',
				'religion'       => 'Christian',
				'hometown'       => 'Akron, Ohio, USA',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'LeBron James Jr., Bryce Maximus James, Zhuri Nova James',
				'hobbies'        => 'Golf, Gaming, Music Production',
				'awards'         => 'NBA Champion (4x), NBA Finals MVP (4x), NBA MVP (4x), Olympic Gold Medal (2x), All-Star (20x)',
				'net_worth'      => '$1 Billion',
				'monthly_earn'   => '$8 Million',
			),
			array(
				'name'           => 'Stephen Curry',
				'height_cm'      => 188.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Basketball',
				'aliases'        => 'Steph',
				'dob'            => '1988-03-14',
				'birthplace'     => 'Charlotte, North Carolina, USA',
				'weight_kg'      => 84,
				'volume'         => 33000,
				'birth_name'     => 'Wardell Stephen Curry II',
				'full_name'      => 'Wardell Stephen Curry II',
				'nickname'       => 'Chef Curry, Baby-Faced Assassin',
				'profession'     => 'Basketball Player',
				'school'         => 'Charlotte Christian School',
				'college'        => 'Davidson College',
				'father_name'    => 'Dell Curry',
				'mother_name'    => 'Sonya Curry',
				'siblings'       => 'Seth Curry, Sydel Curry',
				'marital_status' => 'Married',
				'wife_name'      => 'Ayesha Alexander',
				'girlfriend'     => 'N/A',
				'friends'        => 'Klay Thompson, Draymond Green',
				'religion'       => 'Christian',
				'hometown'       => 'Charlotte, North Carolina, USA',
				'address'        => 'Atherton, California, USA',
				'children'       => 'Riley Curry, Ryan Carson Curry, Canon W. Jack Curry',
				'hobbies'        => 'Golf, Cooking, Ping Pong',
				'awards'         => 'NBA Champion (4x), NBA MVP (2x), All-Star (10x), Three-Point Contest Champion (3x)',
				'net_worth'      => '$160 Million',
				'monthly_earn'   => '$3.5 Million',
			),
			array(
				'name'           => 'Kevin Durant',
				'height_cm'      => 208.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Basketball',
				'aliases'        => 'KD',
				'dob'            => '1988-09-29',
				'birthplace'     => 'Washington D.C., USA',
				'weight_kg'      => 109,
				'volume'         => 28000,
				'birth_name'     => 'Kevin Wayne Durant',
				'full_name'      => 'Kevin Wayne Durant',
				'nickname'       => 'KD, Slim Reaper, The Servant',
				'profession'     => 'Basketball Player',
				'school'         => 'Seat Pleasant Activity Center',
				'college'        => 'University of Texas',
				'father_name'    => 'Wayne Pratt',
				'mother_name'    => 'Wanda Durant',
				'siblings'       => 'Tony Durant, Brianna Durant, Raul Durant',
				'marital_status' => 'Single',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'LeBron James, Kyrie Irving',
				'religion'       => 'Christian',
				'hometown'       => 'Prince George\'s County, Maryland, USA',
				'address'        => 'Phoenix, Arizona, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Gaming, Music, Hiking',
				'awards'         => 'NBA Champion (2x), NBA Finals MVP (2x), NBA MVP, Olympic Gold (2x), All-Star (14x)',
				'net_worth'      => '$200 Million',
				'monthly_earn'   => '$4 Million',
			),
			array(
				'name'           => 'Giannis Antetokounmpo',
				'height_cm'      => 211.0,
				'gender'         => 'male',
				'country'        => 'GR',
				'category'       => 'Basketball',
				'aliases'        => 'Greek Freak',
				'dob'            => '1994-12-06',
				'birthplace'     => 'Athens, Greece',
				'weight_kg'      => 110,
				'volume'         => 32000,
				'birth_name'     => 'Giannis Sina Ugo Antetokounmpo',
				'full_name'      => 'Giannis Sina Ugo Antetokounmpo',
				'nickname'       => 'Greek Freak, The Alphabet',
				'profession'     => 'Basketball Player',
				'school'         => 'Filathlitikos BC Academy',
				'college'        => 'N/A',
				'father_name'    => 'Charles Antetokounmpo',
				'mother_name'    => 'Veronica Antetokounmpo',
				'siblings'       => 'Thanasis Antetokounmpo, Kostas Antetokounmpo, Alex Antetokounmpo, Francis Antetokounmpo',
				'marital_status' => 'Married',
				'wife_name'      => 'Mariah Riddlesprigger',
				'girlfriend'     => 'N/A',
				'friends'        => 'Thanasis Antetokounmpo, Khris Middleton',
				'religion'       => 'Christian',
				'hometown'       => 'Athens, Greece',
				'address'        => 'Milwaukee, Wisconsin, USA',
				'children'       => 'Liam Charles Antetokounmpo, Maverick Shai Antetokounmpo, Kareem Antetokounmpo',
				'hobbies'        => 'Family Time, Football (Soccer), Cooking',
				'awards'         => 'NBA Champion, NBA Finals MVP, NBA MVP (2x), Defensive Player of the Year (2x), All-Star (9x)',
				'net_worth'      => '$70 Million',
				'monthly_earn'   => '$2.5 Million',
			),
			array(
				'name'           => "Shaquille O'Neal",
				'height_cm'      => 216.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Basketball',
				'aliases'        => 'Shaq',
				'dob'            => '1972-03-06',
				'birthplace'     => 'Newark, New Jersey, USA',
				'weight_kg'      => 147,
				'volume'         => 22200,
				'birth_name'     => 'Shaquille Rashaun O\'Neal',
				'full_name'      => 'Shaquille Rashaun O\'Neal',
				'nickname'       => 'Shaq, The Big Aristotle, Diesel',
				'profession'     => 'Retired Basketball Player, Businessman',
				'school'         => 'Cole High School',
				'college'        => 'Louisiana State University',
				'father_name'    => 'Philip Arthur Harrison',
				'mother_name'    => 'Lucille O\'Neal',
				'siblings'       => 'Jamal Harrison, Ayesha Harrison, Lateefah Harrison',
				'marital_status' => 'Divorced',
				'wife_name'      => 'Shaunie Nelson (divorced)',
				'girlfriend'     => 'N/A',
				'friends'        => 'Charles Barkley, Kobe Bryant',
				'religion'       => 'Muslim',
				'hometown'       => 'Newark, New Jersey, USA',
				'address'        => 'Orlando, Florida, USA',
				'children'       => 'Shareef O\'Neal, Amirah O\'Neal, Shaqir O\'Neal, Me\'arah O\'Neal',
				'hobbies'        => 'DJing, Comedy, Investing',
				'awards'         => 'NBA Champion (4x), NBA Finals MVP (3x), NBA MVP, Olympic Gold 1996, Hall of Fame',
				'net_worth'      => '$400 Million',
				'monthly_earn'   => '$2 Million',
			),
		),

		'Actors' => array(
			array(
				'name'           => 'Dwayne Johnson',
				'height_cm'      => 196.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Actor',
				'aliases'        => 'The Rock',
				'dob'            => '1972-05-02',
				'birthplace'     => 'Hayward, California, USA',
				'weight_kg'      => 118,
				'volume'         => 27100,
				'birth_name'     => 'Dwayne Douglas Johnson',
				'full_name'      => 'Dwayne Douglas Johnson',
				'nickname'       => 'The Rock',
				'profession'     => 'Actor, Producer, Former Wrestler',
				'school'         => 'Freedom High School',
				'college'        => 'University of Miami',
				'father_name'    => 'Rocky Johnson',
				'mother_name'    => 'Ata Johnson',
				'siblings'       => 'Curtis Bowles, Wanda Bowles',
				'marital_status' => 'Married',
				'wife_name'      => 'Lauren Hashian',
				'girlfriend'     => 'N/A',
				'friends'        => 'Kevin Hart, Vin Diesel',
				'religion'       => 'Christian',
				'hometown'       => 'Hayward, California, USA',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'Simone Alexandra Johnson, Jasmine Johnson, Tiana Gia Johnson',
				'hobbies'        => 'Workout, Cooking, Football',
				'awards'         => 'Teen Choice Award, People\'s Choice Award, MTV Generation Award, Hollywood Walk of Fame',
				'net_worth'      => '$800 Million',
				'monthly_earn'   => '$10 Million',
			),
			array(
				'name'           => 'Tom Cruise',
				'height_cm'      => 170.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Actor',
				'aliases'        => '',
				'dob'            => '1962-07-03',
				'birthplace'     => 'Syracuse, New York, USA',
				'weight_kg'      => 67,
				'volume'         => 60500,
				'birth_name'     => 'Thomas Cruise Mapother IV',
				'full_name'      => 'Thomas Cruise Mapother IV',
				'nickname'       => 'Tom',
				'profession'     => 'Actor, Producer, Stuntman',
				'school'         => 'Glen Ridge High School',
				'college'        => 'N/A',
				'father_name'    => 'Thomas Cruise Mapother III',
				'mother_name'    => 'Mary Lee Pfeiffer',
				'siblings'       => 'Lee Ann Mapother, Marian Mapother, Cass Mapother',
				'marital_status' => 'Divorced',
				'wife_name'      => 'Katie Holmes (divorced)',
				'girlfriend'     => 'N/A',
				'friends'        => 'David Beckham, Will Smith',
				'religion'       => 'Scientology',
				'hometown'       => 'Syracuse, New York, USA',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'Connor Cruise, Isabella Cruise, Suri Cruise',
				'hobbies'        => 'Skydiving, Motorbike Riding, Hiking',
				'awards'         => 'Golden Globe Award (3x), Empire Award, César Award, Academy Award Nominations',
				'net_worth'      => '$600 Million',
				'monthly_earn'   => '$5 Million',
			),
			array(
				'name'           => 'Brad Pitt',
				'height_cm'      => 180.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Actor',
				'aliases'        => '',
				'dob'            => '1963-12-18',
				'birthplace'     => 'Shawnee, Oklahoma, USA',
				'weight_kg'      => 78,
				'volume'         => 30000,
				'birth_name'     => 'William Bradley Pitt',
				'full_name'      => 'William Bradley Pitt',
				'nickname'       => 'Brad',
				'profession'     => 'Actor, Producer',
				'school'         => 'Kickapoo High School',
				'college'        => 'University of Missouri',
				'father_name'    => 'William Alvin Pitt',
				'mother_name'    => 'Jane Etta Hillhouse',
				'siblings'       => 'Doug Pitt, Julie Neal',
				'marital_status' => 'Divorced',
				'wife_name'      => 'Jennifer Aniston (divorced), Angelina Jolie (divorced)',
				'girlfriend'     => 'Ines de Ramon',
				'friends'        => 'George Clooney, Matt Damon',
				'religion'       => 'Christian',
				'hometown'       => 'Springfield, Missouri, USA',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'Maddox Chivan Jolie-Pitt, Zahara Marley Jolie-Pitt, Pax Thien Jolie-Pitt, Shiloh Nouvel Jolie-Pitt, Vivienne Marcheline Jolie-Pitt, Knox Leon Jolie-Pitt',
				'hobbies'        => 'Sculpting, Architecture, Motorcycle Riding',
				'awards'         => 'Academy Award, BAFTA Award, Golden Globe Award, Screen Actors Guild Award',
				'net_worth'      => '$400 Million',
				'monthly_earn'   => '$3 Million',
			),
			array(
				'name'           => 'Keanu Reeves',
				'height_cm'      => 186.0,
				'gender'         => 'male',
				'country'        => 'CA',
				'category'       => 'Actor',
				'aliases'        => '',
				'dob'            => '1964-09-02',
				'birthplace'     => 'Beirut, Lebanon',
				'weight_kg'      => 84,
				'volume'         => 25000,
				'birth_name'     => 'Keanu Charles Reeves',
				'full_name'      => 'Keanu Charles Reeves',
				'nickname'       => 'The One',
				'profession'     => 'Actor, Director, Musician',
				'school'         => 'De La Salle College',
				'college'        => 'N/A',
				'father_name'    => 'Samuel Nowlin Reeves Jr.',
				'mother_name'    => 'Patricia Taylor',
				'siblings'       => 'Kim Reeves, Karina Miller',
				'marital_status' => 'In a relationship',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'Alexandra Grant',
				'friends'        => 'Alex Winter, Chad Stahelski',
				'religion'       => 'Buddhist',
				'hometown'       => 'Toronto, Ontario, Canada',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Motorcycling, Reading, Guitar',
				'awards'         => 'MTV Movie Award, People\'s Choice Award, Hollywood Walk of Fame',
				'net_worth'      => '$380 Million',
				'monthly_earn'   => '$2.5 Million',
			),
			array(
				'name'           => 'Chris Hemsworth',
				'height_cm'      => 190.0,
				'gender'         => 'male',
				'country'        => 'AU',
				'category'       => 'Actor',
				'aliases'        => 'Thor',
				'dob'            => '1983-08-11',
				'birthplace'     => 'Melbourne, Victoria, Australia',
				'weight_kg'      => 91,
				'volume'         => 20000,
				'birth_name'     => 'Christopher Hemsworth',
				'full_name'      => 'Christopher Hemsworth',
				'nickname'       => 'Thor',
				'profession'     => 'Actor',
				'school'         => 'Heathmont College',
				'college'        => 'N/A',
				'father_name'    => 'Craig Hemsworth',
				'mother_name'    => 'Leonie Van Os',
				'siblings'       => 'Luke Hemsworth, Liam Hemsworth',
				'marital_status' => 'Married',
				'wife_name'      => 'Elsa Pataky',
				'girlfriend'     => 'N/A',
				'friends'        => 'Tom Hiddleston, Mark Ruffalo',
				'religion'       => 'Christian',
				'hometown'       => 'Melbourne, Victoria, Australia',
				'address'        => 'Byron Bay, New South Wales, Australia',
				'children'       => 'India Rose Hemsworth, Tristan Hemsworth, Sasha Hemsworth',
				'hobbies'        => 'Surfing, Workout, Hiking',
				'awards'         => 'People\'s Choice Award, MTV Movie Award, Australian Academy Award',
				'net_worth'      => '$130 Million',
				'monthly_earn'   => '$2 Million',
			),
		),

		'Actresses' => array(
			array(
				'name'           => 'Zendaya',
				'height_cm'      => 178.0,
				'gender'         => 'female',
				'country'        => 'US',
				'category'       => 'Actress',
				'aliases'        => '',
				'dob'            => '1996-09-01',
				'birthplace'     => 'Oakland, California, USA',
				'weight_kg'      => 59,
				'volume'         => 33100,
				'birth_name'     => 'Zendaya Maree Stoermer Coleman',
				'full_name'      => 'Zendaya Maree Stoermer Coleman',
				'nickname'       => 'Daya',
				'profession'     => 'Actress, Singer, Producer',
				'school'         => 'Oakland School for the Arts',
				'college'        => 'N/A',
				'father_name'    => 'Kazembe Ajamu Coleman',
				'mother_name'    => 'Claire Stoermer',
				'siblings'       => 'Katianna Stoermer Coleman, Julien Stoermer Coleman, Kaylee Stoermer Coleman, Austin Stoermer Coleman, Annabella Stoermer Coleman',
				'marital_status' => 'In a relationship',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Tom Holland, Bella Thorne',
				'religion'       => 'Christian',
				'hometown'       => 'Oakland, California, USA',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Fashion Design, Dancing, Singing',
				'awards'         => 'Emmy Award (2x), Saturn Award, Screen Actors Guild Award, MTV Movie Award',
				'net_worth'      => '$25 Million',
				'monthly_earn'   => '$800,000',
			),
			array(
				'name'           => 'Margot Robbie',
				'height_cm'      => 168.0,
				'gender'         => 'female',
				'country'        => 'AU',
				'category'       => 'Actress',
				'aliases'        => '',
				'dob'            => '1990-07-02',
				'birthplace'     => 'Dalby, Queensland, Australia',
				'weight_kg'      => 54,
				'volume'         => 22000,
				'birth_name'     => 'Margot Elise Robbie',
				'full_name'      => 'Margot Elise Robbie',
				'nickname'       => 'Maggot',
				'profession'     => 'Actress, Producer',
				'school'         => 'Somerset College',
				'college'        => 'N/A',
				'father_name'    => 'Douglas Robbie',
				'mother_name'    => 'Sarie Kessler',
				'siblings'       => 'Lachlan Robbie, Cameron Robbie, Anya Robbie',
				'marital_status' => 'Married',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Cara Delevingne, Gal Gadot',
				'religion'       => 'Christian',
				'hometown'       => 'Dalby, Queensland, Australia',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Surfing, Ice Skating, Reading',
				'awards'         => 'BAFTA Award, Screen Actors Guild Award, Critics\' Choice Award, Academy Award Nominations',
				'net_worth'      => '$40 Million',
				'monthly_earn'   => '$1 Million',
			),
			array(
				'name'           => 'Scarlett Johansson',
				'height_cm'      => 160.0,
				'gender'         => 'female',
				'country'        => 'US',
				'category'       => 'Actress',
				'aliases'        => 'ScarJo',
				'dob'            => '1984-11-22',
				'birthplace'     => 'New York City, New York, USA',
				'weight_kg'      => 54,
				'volume'         => 28000,
				'birth_name'     => 'Scarlett Ingrid Johansson',
				'full_name'      => 'Scarlett Ingrid Johansson',
				'nickname'       => 'ScarJo',
				'profession'     => 'Actress, Singer',
				'school'         => 'Professional Children\'s School',
				'college'        => 'N/A',
				'father_name'    => 'Karsten Johansson',
				'mother_name'    => 'Melanie Sloan',
				'siblings'       => 'Adrian Johansson, Felix Johansson, Vanessa Johansson, Hunter Johansson',
				'marital_status' => 'Married',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Florence Pugh, Jennifer Lawrence',
				'religion'       => 'Jewish',
				'hometown'       => 'New York City, New York, USA',
				'address'        => 'New York City, New York, USA',
				'children'       => 'Rose Dorothy Dauriac, Cosmo',
				'hobbies'        => 'Singing, Cooking, Reading',
				'awards'         => 'Tony Award, BAFTA Award, Screen Actors Guild Award, Academy Award Nominations (2x)',
				'net_worth'      => '$165 Million',
				'monthly_earn'   => '$1.5 Million',
			),
			array(
				'name'           => 'Jennifer Lawrence',
				'height_cm'      => 175.0,
				'gender'         => 'female',
				'country'        => 'US',
				'category'       => 'Actress',
				'aliases'        => 'J-Law',
				'dob'            => '1990-08-15',
				'birthplace'     => 'Louisville, Kentucky, USA',
				'weight_kg'      => 60,
				'volume'         => 19000,
				'birth_name'     => 'Jennifer Shrader Lawrence',
				'full_name'      => 'Jennifer Shrader Lawrence',
				'nickname'       => 'J-Law',
				'profession'     => 'Actress, Producer',
				'school'         => 'Kammerer Middle School',
				'college'        => 'N/A',
				'father_name'    => 'Gary Lawrence',
				'mother_name'    => 'Karen Koch',
				'siblings'       => 'Ben Lawrence, Blaine Lawrence',
				'marital_status' => 'Married',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Emma Stone, Adele',
				'religion'       => 'Christian',
				'hometown'       => 'Louisville, Kentucky, USA',
				'address'        => 'New York City, New York, USA',
				'children'       => 'Cy Heatherton',
				'hobbies'        => 'Archery, Cooking, Hiking',
				'awards'         => 'Academy Award for Best Actress, Golden Globe Award (2x), BAFTA Award, Screen Actors Guild Award',
				'net_worth'      => '$160 Million',
				'monthly_earn'   => '$3 Million',
			),
			array(
				'name'           => 'Emma Watson',
				'height_cm'      => 165.0,
				'gender'         => 'female',
				'country'        => 'GB',
				'category'       => 'Actress',
				'aliases'        => 'Hermione',
				'dob'            => '1990-04-15',
				'birthplace'     => 'Paris, France',
				'weight_kg'      => 55,
				'volume'         => 18000,
				'birth_name'     => 'Emma Charlotte Duerre Watson',
				'full_name'      => 'Emma Charlotte Duerre Watson',
				'nickname'       => 'Em',
				'profession'     => 'Actress, Activist, Model',
				'school'         => 'Dragon School',
				'college'        => 'Brown University',
				'father_name'    => 'Chris Watson',
				'mother_name'    => 'Jacqueline Luesby',
				'siblings'       => 'Alex Watson',
				'marital_status' => 'In a relationship',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Daniel Radcliffe, Rupert Grint',
				'religion'       => 'Christian',
				'hometown'       => 'Oxfordshire, England',
				'address'        => 'London, United Kingdom',
				'children'       => 'N/A',
				'hobbies'        => 'Reading, Yoga, Knitting',
				'awards'         => 'BAFTA Children\'s Award, MTV Movie Award (8x), Teen Choice Award, People\'s Choice Award',
				'net_worth'      => '$85 Million',
				'monthly_earn'   => '$600,000',
			),
		),

		'Musicians' => array(
			array(
				'name'           => 'Taylor Swift',
				'height_cm'      => 178.0,
				'gender'         => 'female',
				'country'        => 'US',
				'category'       => 'Musician',
				'aliases'        => '',
				'dob'            => '1989-12-13',
				'birthplace'     => 'West Reading, Pennsylvania, USA',
				'weight_kg'      => 57,
				'volume'         => 49500,
				'birth_name'     => 'Taylor Alison Swift',
				'full_name'      => 'Taylor Alison Swift',
				'nickname'       => 'T-Swift',
				'profession'     => 'Singer-Songwriter, Actress',
				'school'         => 'Hendersonville High School',
				'college'        => 'N/A',
				'father_name'    => 'Scott Kingsley Swift',
				'mother_name'    => 'Andrea Gardner Swift',
				'siblings'       => 'Austin Swift',
				'marital_status' => 'In a relationship',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Selena Gomez, Blake Lively',
				'religion'       => 'Christian',
				'hometown'       => 'Wyomissing, Pennsylvania, USA',
				'address'        => 'Nashville, Tennessee, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Baking, Songwriting, Cat Collecting',
				'awards'         => 'Grammy Award (14x), Billboard Music Award (40x), American Music Award (40x), MTV Video Music Award',
				'net_worth'      => '$1.1 Billion',
				'monthly_earn'   => '$80 Million',
			),
			array(
				'name'           => 'Ariana Grande',
				'height_cm'      => 154.0,
				'gender'         => 'female',
				'country'        => 'US',
				'category'       => 'Musician',
				'aliases'        => 'Ari',
				'dob'            => '1993-06-26',
				'birthplace'     => 'Boca Raton, Florida, USA',
				'weight_kg'      => 48,
				'volume'         => 40500,
				'birth_name'     => 'Ariana Grande-Butera',
				'full_name'      => 'Ariana Grande-Butera',
				'nickname'       => 'Ari',
				'profession'     => 'Singer, Actress, Songwriter',
				'school'         => 'North Broward Preparatory School',
				'college'        => 'N/A',
				'father_name'    => 'Edward Butera',
				'mother_name'    => 'Joan Grande',
				'siblings'       => 'Frankie Grande',
				'marital_status' => 'Married',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Liz Gillies, Jennifer Lawrence',
				'religion'       => 'Jewish (Kabbalah)',
				'hometown'       => 'Boca Raton, Florida, USA',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Horror Movies, Astrology, Piano',
				'awards'         => 'Grammy Award (2x), Billboard Music Award (8x), MTV Video Music Award (7x), American Music Award',
				'net_worth'      => '$200 Million',
				'monthly_earn'   => '$2 Million',
			),
			array(
				'name'           => 'Ed Sheeran',
				'height_cm'      => 173.0,
				'gender'         => 'male',
				'country'        => 'GB',
				'category'       => 'Musician',
				'aliases'        => '',
				'dob'            => '1991-02-17',
				'birthplace'     => 'Halifax, West Yorkshire, England',
				'weight_kg'      => 70,
				'volume'         => 35000,
				'birth_name'     => 'Edward Christopher Sheeran',
				'full_name'      => 'Edward Christopher Sheeran',
				'nickname'       => 'Teddy',
				'profession'     => 'Singer-Songwriter, Record Producer',
				'school'         => 'Thomas Mills High School',
				'college'        => 'Academy of Contemporary Music',
				'father_name'    => 'John Sheeran',
				'mother_name'    => 'Imogen Lock',
				'siblings'       => 'Matthew Sheeran',
				'marital_status' => 'Married',
				'wife_name'      => 'Cherry Seaborn',
				'girlfriend'     => 'N/A',
				'friends'        => 'Taylor Swift, Harry Styles',
				'religion'       => 'Christian',
				'hometown'       => 'Framlingham, Suffolk, England',
				'address'        => 'Framlingham, Suffolk, England',
				'children'       => 'Lyra Antarctica Seaborn Sheeran, Jupiter Seaborn Sheeran',
				'hobbies'        => 'Cooking, Gaming, Collecting Art',
				'awards'         => 'Grammy Award (4x), Brit Award (5x), Billboard Music Award, Ivor Novello Award',
				'net_worth'      => '$280 Million',
				'monthly_earn'   => '$2.5 Million',
			),
			array(
				'name'           => 'Bruno Mars',
				'height_cm'      => 165.0,
				'gender'         => 'male',
				'country'        => 'US',
				'category'       => 'Musician',
				'aliases'        => '',
				'dob'            => '1985-10-08',
				'birthplace'     => 'Honolulu, Hawaii, USA',
				'weight_kg'      => 64,
				'volume'         => 22000,
				'birth_name'     => 'Peter Gene Hernandez',
				'full_name'      => 'Peter Gene Hernandez',
				'nickname'       => 'Bruno',
				'profession'     => 'Singer, Songwriter, Musician',
				'school'         => 'President Theodore Roosevelt High School',
				'college'        => 'N/A',
				'father_name'    => 'Pete Hernandez',
				'mother_name'    => 'Bernadette San Pedro Bayot',
				'siblings'       => 'Eric Hernandez, Tiara Hernandez, Jaime Kailani, Tahiti, Presley',
				'marital_status' => 'In a relationship',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'Jessica Caban',
				'friends'        => 'Cardi B, Anderson Paak',
				'religion'       => 'Catholic',
				'hometown'       => 'Honolulu, Hawaii, USA',
				'address'        => 'Las Vegas, Nevada, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Dancing, Playing Instruments, Poker',
				'awards'         => 'Grammy Award (15x), Billboard Music Award, BET Award, American Music Award',
				'net_worth'      => '$175 Million',
				'monthly_earn'   => '$1.5 Million',
			),
			array(
				'name'           => 'Billie Eilish',
				'height_cm'      => 163.0,
				'gender'         => 'female',
				'country'        => 'US',
				'category'       => 'Musician',
				'aliases'        => '',
				'dob'            => '2001-12-18',
				'birthplace'     => 'Los Angeles, California, USA',
				'weight_kg'      => 57,
				'volume'         => 30000,
				'birth_name'     => 'Billie Eilish Pirate Baird O\'Connell',
				'full_name'      => 'Billie Eilish Pirate Baird O\'Connell',
				'nickname'       => 'Billie',
				'profession'     => 'Singer-Songwriter',
				'school'         => 'Homeschooled',
				'college'        => 'N/A',
				'father_name'    => 'Patrick O\'Connell',
				'mother_name'    => 'Maggie Baird',
				'siblings'       => 'Finneas O\'Connell',
				'marital_status' => 'In a relationship',
				'wife_name'      => 'N/A',
				'girlfriend'     => 'N/A',
				'friends'        => 'Finneas O\'Connell, Taylor Swift',
				'religion'       => 'Christian',
				'hometown'       => 'Los Angeles, California, USA',
				'address'        => 'Los Angeles, California, USA',
				'children'       => 'N/A',
				'hobbies'        => 'Drawing, Dancing, Animal Welfare',
				'awards'         => 'Grammy Award (9x), Oscar Award, Golden Globe Award, Brit Award, Billboard Music Award',
				'net_worth'      => '$50 Million',
				'monthly_earn'   => '$600,000',
			),
		),
	);
}

/**
 * Sample height references: [name, cm, category].
 *
 * @return array<int, array{0: string, 1: float, 2: string}>
 */
function hc_sample_references(): array {
	return array(
		array( 'Average Door', 203.0, 'Object' ),
		array( 'Giraffe', 550.0, 'Animal' ),
		array( 'African Elephant', 330.0, 'Animal' ),
		array( 'Basketball Hoop', 305.0, 'Object' ),
		array( 'Emperor Penguin', 120.0, 'Animal' ),
	);
}

/**
 * Sample country averages: [name, iso, male, female, source, year].
 *
 * @return array<int, array{0: string, 1: string, 2: float, 3: float, 4: string, 5: int}>
 */
function hc_sample_countries(): array {
	return array(
		array( 'United States',   'US', 177.0, 163.0, 'CDC NHANES', 2020 ),
		array( 'United Kingdom',  'GB', 177.8, 163.9, 'NCD-RisC',   2019 ),
		array( 'Netherlands',     'NL', 183.8, 170.4, 'NCD-RisC',   2019 ),
		array( 'Argentina',       'AR', 174.5, 161.0, 'NCD-RisC',   2019 ),
		array( 'Portugal',        'PT', 173.9, 161.0, 'NCD-RisC',   2019 ),
		array( 'Brazil',          'BR', 175.3, 162.5, 'NCD-RisC',   2019 ),
		array( 'France',          'FR', 179.7, 165.6, 'NCD-RisC',   2019 ),
		array( 'Norway',          'NO', 179.8, 166.5, 'NCD-RisC',   2019 ),
		array( 'Greece',          'GR', 177.3, 165.7, 'NCD-RisC',   2019 ),
		array( 'Canada',          'CA', 177.8, 163.9, 'NCD-RisC',   2019 ),
		array( 'Australia',       'AU', 175.6, 161.8, 'NCD-RisC',   2019 ),
	);
}

/**
 * Register the admin tools page.
 */
function hc_sample_menu(): void {
	add_management_page(
		'Height Compare — Sample Data',
		'HC Sample Data',
		'manage_options',
		'hc-sample-data',
		'hc_sample_page'
	);
}
add_action( 'admin_menu', 'hc_sample_menu' );

/**
 * Render + handle the seeding page.
 */
function hc_sample_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$done   = 0;
	$purged = 0;
	$msg    = '';

	if ( isset( $_POST['hc_seed_nonce'] )
		&& wp_verify_nonce( sanitize_key( wp_unslash( $_POST['hc_seed_nonce'] ) ), 'hc_seed' )
	) {
		if ( isset( $_POST['hc_purge_reseed'] ) ) {
			$purged = hc_purge_celebrities();
			$done   = hc_seed_sample_data();
			$msg    = sprintf( 'Purged %d celebrities and seeded %d new items.', $purged, $done );
		} elseif ( isset( $_POST['hc_seed'] ) ) {
			$done = hc_seed_sample_data();
			$msg  = sprintf( 'Seeded %d new items.', $done );
		}
	}

	echo '<div class="wrap"><h1>Height Compare — Sample Data</h1>';
	if ( '' !== $msg ) {
		printf( '<div class="notice notice-success"><p>%s</p></div>', esc_html( $msg ) );
	}
	echo '<form method="post">';
	wp_nonce_field( 'hc_seed', 'hc_seed_nonce' );
	echo '<p>Creates sample celebrities (5 per group with full biographical data), references and country averages.</p>';
	echo '<p>
		<button class="button button-primary" name="hc_seed" value="1">Seed sample data (skip existing)</button>
		&nbsp;&nbsp;
		<button class="button" name="hc_purge_reseed" value="1"
			onclick="return confirm(\'Delete ALL celebrities and re-seed 5 per group?\');"
			style="border-color:#d63638;color:#d63638;">Purge &amp; Re-seed</button>
	</p></form></div>';
}

/**
 * Delete every celebrity post and return the count removed.
 */
function hc_purge_celebrities(): int {
	$posts = get_posts(
		array(
			'post_type'      => 'celebrity',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	$count = 0;
	foreach ( $posts as $id ) {
		wp_delete_post( (int) $id, true );
		++$count;
	}
	return $count;
}

/**
 * Insert sample posts. Returns the number created.
 */
function hc_seed_sample_data(): int {
	$created = 0;

	foreach ( hc_sample_celebrities() as $group_name => $celebrities ) {
		$term = term_exists( $group_name, 'celebrity_group' );
		if ( ! $term ) {
			$result = wp_insert_term( $group_name, 'celebrity_group' );
			$term   = is_wp_error( $result ) ? null : array( 'term_id' => $result['term_id'] );
		}
		$term_id = is_array( $term ) ? (int) ( $term['term_id'] ?? 0 ) : 0;

		foreach ( $celebrities as $c ) {
			$id = hc_seed_post( 'celebrity', $c['name'] );
			if ( $id > 0 ) {
				update_post_meta( $id, 'hc_height_cm',      $c['height_cm'] );
				update_post_meta( $id, 'hc_gender',         $c['gender'] );
				update_post_meta( $id, 'hc_country',        $c['country'] );
				update_post_meta( $id, 'hc_category',       $c['category'] );
				update_post_meta( $id, 'hc_dob',            $c['dob'] );
				update_post_meta( $id, 'hc_birthplace',     $c['birthplace'] );
				update_post_meta( $id, 'hc_weight_kg',      $c['weight_kg'] );
				update_post_meta( $id, 'hc_search_volume',  $c['volume'] );
				update_post_meta( $id, 'hc_birth_name',     $c['birth_name'] );
				update_post_meta( $id, 'hc_full_name',      $c['full_name'] );
				update_post_meta( $id, 'hc_nickname',       $c['nickname'] );
				update_post_meta( $id, 'hc_profession',     $c['profession'] );
				update_post_meta( $id, 'hc_school',         $c['school'] );
				update_post_meta( $id, 'hc_college',        $c['college'] );
				update_post_meta( $id, 'hc_father_name',    $c['father_name'] );
				update_post_meta( $id, 'hc_mother_name',    $c['mother_name'] );
				update_post_meta( $id, 'hc_siblings',       $c['siblings'] );
				update_post_meta( $id, 'hc_marital_status', $c['marital_status'] );
				update_post_meta( $id, 'hc_wife_name',      $c['wife_name'] );
				update_post_meta( $id, 'hc_girlfriend_name',$c['girlfriend'] );
				update_post_meta( $id, 'hc_friends_names',  $c['friends'] );
				update_post_meta( $id, 'hc_religion',       $c['religion'] );
				update_post_meta( $id, 'hc_hometown',       $c['hometown'] );
				update_post_meta( $id, 'hc_current_address',$c['address'] );
				update_post_meta( $id, 'hc_children',       $c['children'] );
				update_post_meta( $id, 'hc_hobbies',        $c['hobbies'] );
				update_post_meta( $id, 'hc_awards',         $c['awards'] );
				update_post_meta( $id, 'hc_net_worth',      $c['net_worth'] );
				update_post_meta( $id, 'hc_monthly_earning',$c['monthly_earn'] );
				if ( '' !== $c['aliases'] ) {
					update_post_meta( $id, 'hc_aliases', $c['aliases'] );
				}

				if ( $term_id > 0 ) {
					wp_set_object_terms( $id, $term_id, 'celebrity_group', true );
				}
				hc_assign_age_group( $id );
				hc_assign_height_group( $id );
				++$created;
			}
		}
	}

	foreach ( hc_sample_references() as $row ) {
		$id = hc_seed_post( 'height_reference', $row[0] );
		if ( $id > 0 ) {
			update_post_meta( $id, 'hc_height_cm', $row[1] );
			update_post_meta( $id, 'hc_category',  $row[2] );
			++$created;
		}
	}

	foreach ( hc_sample_countries() as $row ) {
		$id = hc_seed_post( 'country_average', $row[0] );
		if ( $id > 0 ) {
			update_post_meta( $id, 'hc_iso_code',      $row[1] );
			update_post_meta( $id, 'hc_avg_male_cm',   $row[2] );
			update_post_meta( $id, 'hc_avg_female_cm', $row[3] );
			update_post_meta( $id, 'hc_source',        $row[4] );
			update_post_meta( $id, 'hc_year',          $row[5] );
			++$created;
		}
	}

	hc_flush_preset_cache();
	return $created;
}

/**
 * Insert a post if none with the same title/type exists.
 */
function hc_seed_post( string $post_type, string $title ): int {
	$existing = get_posts(
		array(
			'post_type'      => $post_type,
			'title'          => $title,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( array() !== $existing ) {
		return 0;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => '',
		)
	);
	return ( $id > 0 ) ? (int) $id : 0;
}
