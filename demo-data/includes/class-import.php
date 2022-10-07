<?php

namespace WPAdverts\Snippets\Demo\Data;

class Import {

    protected $extract_from = '/zip/files-v2.zip';

    protected $extract_to = '/files/';

    /**
     * Path to /includes/ folder
     */
    protected $path = null;

    /**
     * List of setups
     */
    protected $setup = array();

    /**
     * List of terms to upload
     *
     * @var array 
     */
    protected $terms = null;
    
    /**
     * List of posts to upload
     *
     * @var array
     */
    protected $posts = null;
    
    /**
     * List of ads saved in the database
     * 
     * @var array[WP_Post]
     */
    protected $saved = array();
    
    public function __construct( $path = null ) {
        $this->path = $path;

    }

    public function load( $class ) {
        include_once sprintf( "%s/includes/%s.php", $this->path, $class );
    }

    public function publish_adverts_add() {
        $posts = get_posts( array(
            "post_type" => "page",
            "post_status" => "draft",
            "s" => "adverts_add"
        ) );
        
        foreach( $posts as $post ) {
            wp_update_post( array(
                "ID" => $post->ID,
                "post_status" => "publish"
            ) );
        }
    }
    
    /**
     * Returns list of terms
     * 
     * @return array
     */ 
    protected function _get_terms()
    {
        if( $this->terms !== null ) {
            return $this->terms;
        }
        
        $this->terms = array(
            "video-games" => array("id" => null, "name" => "Video Games", "parent" => null, "icon" => "gamepad"),
            "ios-games" => array("id" => null, "name" => "iOS Games", "parent" => "video-games", "icon" => ""),
            "android-games" => array("id" => null, "name" => "Android Games", "parent" => "video-games", "icon" => ""),
            "playstation-games" => array("id" => null, "name" => "PS Games", "parent" => "video-games", "icon" => ""),
            "xbox-games" => array("id" => null, "name" => "XBox Games", "parent" => "video-games", "icon" => ""),
            "pc-games" => array("id" => null, "name" => "PC Games", "parent" => "video-games", "icon" => ""),

            "books-all" => array("id" => null, "name" => "Books", "parent" => null, "icon" => "book"),
            "books" => array("id" => null, "name" => "Books", "parent" => "books-all", "icon" => ""),
            "textbooks" => array("id" => null, "name" => "Textbooks", "parent" => "books-all", "icon" => ""),
            "magazines" => array("id" => null, "name" => "Magazines", "parent" => "books-all", "icon" => ""),
            "audiobooks" => array("id" => null, "name" => "Audiobooks", "parent" => "books-all", "icon" => ""),
            "comics" => array("id" => null, "name" => "Comics", "parent" => "books-all", "icon" => ""),

            "general" => array("id" => null, "name" => "General", "parent" => null, "icon" => "globe"),
            "arts-and-crafts" => array("id" => null, "name" => "Arts and Crafts", "parent" => "general", "icon" => ""),
            "hair-salon" => array("id" => null, "name" => "Hair Salon", "parent" => "general", "icon" => ""),
            "junk-for-sale" => array("id" => null, "name" => "Junk For Sale", "parent" => "general", "icon" => ""),
            "junk-removal" => array("id" => null, "name" => "Junk Removal", "parent" => "general", "icon" => ""),
            "other" => array("id" => null, "name" => "Other", "parent" => "general", "icon" => ""),

            "announcements" => array("id" => null, "name" => "Announcements", "parent" => null, "icon" => "megaphone"),
            "auctions" => array("id" => null, "name" => "Auctions", "parent" => "announcements", "icon" => ""),
            "boutiques" => array("id" => null, "name" => "Boutiques", "parent" => "announcements", "icon" => ""),
            "grand-openings" => array("id" => null, "name" => "Grand Openings", "parent" => "announcements", "icon" => ""),
            "reunions" => array("id" => null, "name" => "Reunions", "parent" => "announcements", "icon" => ""),
            "charity" => array("id" => null, "name" => "Charity", "parent" => "announcements", "icon" => ""),

            "auto-parts" => array("id" => null, "name" => "Auto Parts", "parent" => null, "icon" => "cab"),
            "auto-accessories" => array("id" => null, "name" => "Auto Accessories", "parent" => "auto-parts", "icon" => ""),
            "car-audio-and-video" => array("id" => null, "name" => "Car Audio and Video", "parent" => "auto-parts", "icon" => ""),
            "engine-part" => array("id" => null, "name" => "Engine Part", "parent" => "auto-parts", "icon" => ""),
            "truck-shells" => array("id" => null, "name" => "Truck Shells", "parent" => "auto-parts", "icon" => ""),
            "wheels-and-tires - Cars" => array("id" => null, "name" => "Wheels and Tires - Cars", "parent" => "auto-parts", "icon" => ""),

            "baby" => array("id" => null, "name" => "Baby", "parent" => null, "icon" => "child"),
            "baby-clothing" => array("id" => null, "name" => "Baby Clothing", "parent" => "baby", "icon" => ""),
            "backpacks-and-carriers" => array("id" => null, "name" => "Backpacks and Carriers", "parent" => "baby", "icon" => ""),
            "feeding" => array("id" => null, "name" => "Feeding", "parent" => "baby", "icon" => ""),
            "maternity-clothing" => array("id" => null, "name" => "Maternity Clothing", "parent" => "baby", "icon" => ""),
            "other-baby-items" => array("id" => null, "name" => "Other Baby Items", "parent" => "baby", "icon" => ""),

            "home-and-garden" => array("id" => null, "name" => "Home and Garden", "parent" => null, "icon" => "home"),
            "carpet-cleaning" => array("id" => null, "name" => "Carpet Cleaning", "parent" => "home-and-garden", "icon" => ""),
            "concrete-contractors" => array("id" => null, "name" => "Concrete Contractors", "parent" => "home-and-garden", "icon" => ""),
            "curtains-blinds-shutters" => array("id" => null, "name" => "Curtains/Blinds/Shutters", "parent" => "home-and-garden", "icon" => ""),
            "general-contractor" => array("id" => null, "name" => "General Contractor", "parent" => "home-and-garden", "icon" => ""),
            "hot-tubs-and-pools" => array("id" => null, "name" => "Hot Tubs and Pools", "parent" => "home-and-garden", "icon" => ""),

            "computers" => array("id" => null, "name" => "Computers", "parent" => null, "icon" => "laptop"),
            "computer-repair" => array("id" => null, "name" => "Computer Repair", "parent" => "computers", "icon" => ""),
            "desktops" => array("id" => null, "name" => "Desktops", "parent" => "computers", "icon" => ""),
            "laptops" => array("id" => null, "name" => "Laptops", "parent" => "computers", "icon" => ""),
            "printing-services" => array("id" => null, "name" => "Printing Services", "parent" => "computers", "icon" => ""),
            "software" => array("id" => null, "name" => "Software", "parent" => "computers", "icon" => ""),

            "outdoors-and-sporting" => array("id" => null, "name" => "Outdoors", "parent" => null, "icon" => "soccer-ball"),
            "activewear" => array("id" => null, "name" => "Activewear", "parent" => "outdoors-and-sporting", "icon" => ""),
            "bicycles-mountain-bikes" => array("id" => null, "name" => "Mountain Bikes", "parent" => "outdoors-and-sporting", "icon" => ""),
            "bicycles-road-bikes" => array("id" => null, "name" => "Road Bikes", "parent" => "outdoors-and-sporting", "icon" => ""),
            "gyms" => array("id" => null, "name" => "Gyms", "parent" => "outdoors-and-sporting", "icon" => ""),
            "general-sporting-goods" => array("id" => null, "name" => "General Sporting Goods", "parent" => "outdoors-and-sporting", "icon" => ""),

            "pets-and-livestock" => array("id" => null, "name" => "Pets", "parent" => null, "icon" => "paw"),
            "birds" => array("id" => null, "name" => "Birds", "parent" => "pets-and-livestock", "icon" => ""),
            "cats" => array("id" => null, "name" => "Cats", "parent" => "pets-and-livestock", "icon" => ""),
            "dogs" => array("id" => null, "name" => "Dogs", "parent" => "pets-and-livestock", "icon" => ""),
            "goats" => array("id" => null, "name" => "Goats", "parent" => "pets-and-livestock", "icon" => ""),
            "reptiles" => array("id" => null, "name" => "Reptiles", "parent" => "pets-and-livestock", "icon" => ""),

            "toys" => array("id" => null, "name" => "Toys", "parent" => null, "icon" => "rocket"),
            "cction-figures" => array("id" => null, "name" => "Action Figures", "parent" => "toys", "icon" => ""),
            "board-and-card-games" => array("id" => null, "name" => "Board and Card Games", "parent" => "toys", "icon" => ""),
            "educational-toys" => array("id" => null, "name" => "Educational Toys", "parent" => "toys", "icon" => ""),
            "dolls" => array("id" => null, "name" => "Dolls", "parent" => "toys", "icon" => ""),
            "stuffed-animals" => array("id" => null, "name" => "Stuffed Animals", "parent" => "toys", "icon" => ""),

            "industrial" => array("id" => null, "name" => "Industrial", "parent" => null, "icon" => "chart-line"),
            "farm-equipment" => array("id" => null, "name" => "Farm Equipment", "parent" => "industrial", "icon" => ""),
            "ladders" => array("id" => null, "name" => "Ladders", "parent" => "industrial", "icon" => ""),
            "power-and-hand-tools" => array("id" => null, "name" => "Power and Hand Tools", "parent" => "industrial", "icon" => ""),
            "shop-tools" => array("id" => null, "name" => "Shop Tools", "parent" => "industrial", "icon" => ""),
            "tool-storage" => array("id" => null, "name" => "Tool Storage", "parent" => "industrial", "icon" => ""),
        );
        
        return $this->terms;
    }

    public function get_terms() {

        $this->_get_terms();

        $terms = array(
            "clothing" => array("id" => null, "name" => "Clothing", "parent" => null, "icon" => "fas fa-shirt"),
            "backpacks" => array("id" => null, "name" => "Backpacks", "parent" => "clothing", "icon" => ""),
            "hoodies" => array("id" => null, "name" => "Hoodies", "parent" => "clothing", "icon" => ""),
            "jackets" => array("id" => null, "name" => "Jackets", "parent" => "clothing", "icon" => ""),
            "pants" => array("id" => null, "name" => "Pants", "parent" => "clothing", "icon" => ""),
            "t-shirts" => array("id" => null, "name" => "T-shirts", "parent" => "clothing", "icon" => ""),
        );

        $this->terms = array_merge( $terms, $this->_get_terms() );

        unset( $this->terms["announcements"] );
        unset( $this->terms["auctions"] );
        unset( $this->terms["boutiques"] );
        unset( $this->terms["grand-openings"] );
        unset( $this->terms["reunions"] );
        unset( $this->terms["charity"] );

        $this->terms["video-games"]["icon"] = "fas fa-gamepad";
        $this->terms["books-all"]["icon"] = "fas fa-book";
        $this->terms["general"]["icon"] = "fas fa-globe";
        $this->terms["auto-parts"]["icon"] = "fas fa-car";
        $this->terms["baby"]["icon"] = "fas fa-baby";
        $this->terms["home-and-garden"]["icon"] = "fas fa-house-chimney";
        $this->terms["computers"]["icon"] = "fas fa-computer";
        $this->terms["outdoors-and-sporting"]["icon"] = "fas fa-futbol";
        $this->terms["pets-and-livestock"]["icon"] = "fas fa-paw";
        $this->terms["toys"]["icon"] = "fas fa-rocket";
        $this->terms["industrial"]["icon"] = "fas fa-industry";

        return $this->terms;

    }

    /**
     * Returns list of Ads to upload
     * 
     * @return array
     */
    public function get_posts()
    {
        if( $this->posts !== null ) {
            return $this->posts;
        }
        
        $user = wp_get_current_user();

        if( $user ) {
            $user_id = $user->ID;
        } else {
            $user_id = 0;
        }

        $this->posts = array(
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Brown Vintage Backpack",
                "post_name" => "brown-vintage-backpack",
                "advert_category" => "backpacks",
                "post_content" => "Knapsacks are generally utilized by explorers and understudies and are frequently liked to purses for conveying weighty loads or conveying any kind of hardware, due to the restricted ability to convey significant burdens for extensive stretches of time in the hands.
                Enormous rucksacks, used to convey loads more than 10 kilograms (22 lb), as well as more modest games knapsacks (for example running, cycling, climbing and hydration), normally offload the biggest part (up to around 90%) of their weight onto cushioned hip belts, leaving the shoulder lashes principally for balancing out the heap. This works on the possibility to convey weighty burdens, as the hips are more grounded than the shoulders, and furthermore expands dexterity and equilibrium, since the heap rides closer the wearer's own focal point of mass.",
                "adverts_price" => "59.50",
                "adverts_location" => "San Francisco, CA",

                "cf_size" => "",
                "cf_color" => "Brown",
                "cf_fabric" => "Poliester",
                "cf_pattern" => "",

                "_attach" => array(
                    array(
                        "filename" => "backpack-2.png",
                        "title" => "Brown vintage backpack",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "backpack-2-flip.png",
                        "title" => "Great condition best fit for outdoors"
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Black Backpack",
                "post_name" => "black-backpack",
                "post_author" => $user_id,
                "advert_category" => "backpacks",
                "post_content" => "Rucksacks are regularly utilized by explorers and understudies, and are frequently liked to purses for conveying weighty loads or conveying any kind of hardware, in view of the restricted ability to convey significant burdens for extensive stretches of time in the hands.
                Huge rucksacks, used to convey loads more than 10 kilograms (22 lb), as well as more modest games knapsacks (for example running, cycling, climbing and hydration), ordinarily offload the biggest part (up to around 90%) of their weight onto cushioned hip belts, leaving the shoulder lashes predominantly for balancing out the heap. This works on the possibility to convey weighty burdens, as the hips are more grounded than the shoulders, and furthermore expands nimbleness and equilibrium, since the heap rides closer the wearer's own focal point of mass.",
                "adverts_price" => "35.00",
                "adverts_location" => "New York, NY",

                "cf_size" => "",
                "cf_color" => "Black",
                "cf_fabric" => "Poliester",
                "cf_pattern" => "",

                "_attach" => array(
                    array(
                        "filename" => "backpack-1.png",
                        "title" => "Black backpack",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "backpack-1-flip.png",
                        "title" => "For high school and collage students."
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Grayish hoodie with a maze print",
                "post_name" => "grayish-hoodie",
                "advert_category" => "hoodies",
                "post_content" => "Hoodies have turned into a standard design in the U.S., rising above the dress thing's unique utilitarian reason, like pants. This dress thing has found its direction into various styles, all things being equal far as to be worn under a suit coat. Hoodies with zippers are by and large alluded to as zoom up hoodies, while a hoodie without a zipper might be portrayed as a sweatshirt hoodie.
                All through the U.S., it is normal for teens and youthful grown-ups to wear pullovers — regardless of hoods — that show their individual school names or mascots across the chest, either as a feature of a uniform or individual inclination
                
                The hooded pullover is a utilitarian piece of clothing that started during the 1930s for laborers in chilly New York distribution centers and subsequently has been around for north of 80 years. During the 70s and 80s, hoodies were embraced by hip-jump culture as an image of what one columnist named \"cool namelessness and obscure danger\" When the piece of clothing was portrayed in FBI composite drawings of Unabomber Ted Kaczynski, the hoodie became connected to \"undesirable undermining culpability,\" consequently further stating its non-standard imagery.",
                "adverts_price" => "19.99",
                "adverts_location" => "San Francisco, CA",

                "cf_size" => "L",
                "cf_color" => "Gray",
                "cf_fabric" => "Cotton",
                "cf_pattern" => "Print",

                "_attach" => array(
                    array(
                        "filename" => "hoodie-1.png",
                        "title" => "Grayish hoodie with a print",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "hoodie-1-flip.png",
                        "title" => "This hoodie contains an interesting maze print"
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Hoodie with an Aztec like print",
                "post_name" => "green-hoodie",
                "advert_category" => "hoodies",
                "post_content" => "The hooded sweatshirt is a utilitarian piece of clothing that started during the 1930s in the US for laborers in cool New York distribution centers. The earliest attire style was first delivered by Champion during the 1930s in Rochester and showcased to workers working in frigid temperatures in upstate New York. The term hoodie entered well-known use during the 1990s.
                The hoodie became well known during the 1970s, with a few elements adding to its prosperity. Hip-jump culture was created in New York City close to this time and high style likewise took off during this period, as Norma Kamali and other high-profile fashioners embraced and glamorized the new attire. Generally basic to the hoodie's fame during this time was its notable appearance in the blockbuster Rocky film. The ascent of hoodies with college logos started around this time.
                
                By the 1990s, the hoodie had developed into an image of confinement, an assertion of scholarly soul, and a few design assortments",
                "adverts_price" => "25.00",
                "adverts_location" => "Chicago, IL",

                "cf_size" => "M",
                "cf_color" => "Green",
                "cf_fabric" => "Cotton",
                "cf_pattern" => "Print",

                "_attach" => array(
                    array(
                        "filename" => "hoodie-2.png",
                        "title" => "Green hoodie with Aztec like print",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "hoodie-2-flip.png",
                        "title" => "Two color green hoodie"
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Saggy cargo pants",
                "post_name" => "saggy-cargo-pants",
                "advert_category" => "pants",
                "post_content" => "Freight jeans or freight pants, likewise once in a while called battle jeans or battle pants after their unique reason as military workwear, are inexactly cut pants initially intended for unpleasant workplaces and open air exercises, recognized by various enormous utility pockets for conveying devices.
                Freight shorts are an abbreviated form of freight pants, with the legs for the most part reaching out down to approach knee lengths.
                
                Both freight jeans and shorts have since become additionally famous as metropolitan relaxed wear since they are baggy and very helpful for conveying additional things during regular foot trips or while cycling.
                
                A freight pocket is a type of a fix pocket, frequently with accordion folds for expanded limit shut with a fold got by snap, button, magnet, or Velcro normal on battledress and hunting clothing. In certain plans, freight pockets might be concealed inside the legs.",
                "adverts_price" => "34.99",
                "adverts_location" => "Chicago, IL",

                "cf_size" => "L",
                "cf_color" => "Brown",
                "cf_fabric" => "Cotton",
                "cf_pattern" => "",

                "_attach" => array(
                    array(
                        "filename" => "pants-1.png",
                        "title" => "Saggy cargo pants",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "pants-1-flip.png",
                        "title" => "A lot of pockets on the both sides"
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Slim fit designer jeans",
                "post_name" => "slim-fit-designer-jeans",
                "advert_category" => "pants",
                "post_content" => "Americans purchased US$13.8 billion of people's pants in the year finished April 30, 2011, as per statistical surveying firm NPD Group. In any case, just around 1% of pants sold in the U.S. over that year cost more than $50.
                The contrast between the $300 pants and the $30 pants frequently has to do with the texture quality, equipment, washes, plan subtleties, scraped spots, and where they are fabricated. An extravagant sets of pants that have been treated with scraped spots, additional washes, and so on, to separate the denim to accomplish a surface has gone through a specific measure of harm to get the 'well used in' feel. In this sense, costly pants might be more fragile than modest ones.
                
                Pants marks likewise attempt to stand apart from one season to another by utilizing licensed materials, like bolts and sewing, and by utilizing extraordinary washes and troubling strategies.",
                "adverts_price" => "119.00",
                "adverts_location" => "San Francisco, CA",

                "cf_size" => "S",
                "cf_color" => "Blue",
                "cf_fabric" => "Cotton",
                "cf_pattern" => "",


                "_attach" => array(
                    array(
                        "filename" => "pants-2.png",
                        "title" => "Slim fit designer jeans",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "pants-2-flip.png",
                        "title" => "Jeans in timeless classic sky blue color"
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Winter bomber jacket",
                "post_name" => "winter-bomber-jacket",
                "post_author" => $user_id,
                "advert_category" => "jackets",
                "post_content" => "A flight coat is a relaxed coat that was initially made for pilots and in the end turned out to be essential for mainstream society and clothing. It has developed into different styles and outlines, including the 'letterman' coat and the stylish 'plane' coat that is known today.
                During the 1970s and 1990s, flight coats became well known with scooterboys and skinheads. During the 1980s, a baseball-style plane coat became famous. In 1993, a uniform flight coat was worn as the 'public outfit' of the United States for the APEC meeting held in Seattle, Washington. In the mid 2000s, the aircraft coat was famous easygoing wear in hip-jump style. The coat has additionally gotten on with a few police offices across the United States for its durable plan and weighty protection.
                
                Flight coat has had a resurgence in prevalence during the 2010s in road style, and is a remarkable staple of superstars like Kanye West.",
                "adverts_price" => "149.50",
                "adverts_location" => "New York, NY",

                "cf_size" => "L",
                "cf_color" => "Green",
                "cf_fabric" => "Poliester",
                "cf_pattern" => "",

                "_attach" => array(
                    array(
                        "filename" => "jacket-1.png",
                        "title" => "Green winter bomber jacket",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "jacket-1-flip.png",
                        "title" => "Comfy and warm in the winter."
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Down Jacket (very comfy)",
                "post_name" => "down-jacket",
                "advert_category" => "jackets",
                "post_content" => "The down coat, known all the more regularly in the design business as a puffer coat or basically puffer, is a sewn coat that is protected with one or the other duck or geese feathers. Air pockets made by the main part of the quills consider the maintenance of warm air.
                Creators and design powerhouses of 2020 oddball trimmed, brilliant hued variants of the coat picking rather for a more drawn out, knee-length layer with unpretentious shades of beige. Powerhouses keep on lauding the article of clothing for its capacity to work with each outfit event. A new flood during the 1990s keeps on assisting the coat with overwhelming. Mainstream society mirrors this pattern as hip-jump specialists like Kanye West and Drake may both be seen wearing the piece of clothing in late music recordings.",
                "adverts_price" => "179.00",
                "adverts_location" => "San Francisco, CA",

                "cf_size" => "XL",
                "cf_color" => "Black",
                "cf_fabric" => "Poliester",
                "cf_pattern" => "",

                "_attach" => array(
                    array(
                        "filename" => "jacket-2.png",
                        "title" => "Very comfy winter jacket",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "jacket-2-flip.png",
                        "title" => ""
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "White t-shirt with a clown print",
                "post_name" => "white-t-shirt",
                "post_author" => $user_id,
                "advert_category" => "t-shirts",
                "post_content" => "A T-shirt, or tee, is a style of texture shirt named after the T state of its body and sleeves. Generally, it has short sleeves and a round neck area, known as a team neck, which misses the mark on collar. Shirts are for the most part made of a stretchy, light, and economical texture and are not difficult to clean. The T-shirt developed from underpants utilized in the nineteenth 100 years and, during the twentieth 100 years, progressed from underpants to general-utilize easygoing apparel.
                They are commonly made of cotton material in a stockinette or pullover sew, which has an unmistakably malleable surface contrasted with shirts made of woven fabric. A few current variants have a body produced using a constantly sewn tube, delivered on a roundabout weaving machine, with the end goal that the middle has no side creases. The production of T-shirts has become profoundly robotized and may incorporate cutting texture with a laser or a water stream.",
                "adverts_price" => "8.49",
                "adverts_location" => "New York, NY",

                "cf_size" => "L",
                "cf_color" => "White",
                "cf_fabric" => "Cotton",
                "cf_pattern" => "Print",

                "_attach" => array(
                    array(
                        "filename" => "t-shirt-1.png",
                        "title" => "Basic white t-shirt",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "t-shirt-1-flip.png",
                        "title" => "This t-shirt has a clown print"
                    ),
                )
            ),
            array(
                "adverts_person" => "Bob Advertiser",
                "adverts_email" => "user@example.com",
                "adverts_phone" => "555-0110",
                "post_title" => "Medium navy t-shirt",
                "post_name" => "navy-t-shirt",
                "post_author" => $user_id,
                "advert_category" => "t-shirts",
                "post_content" => "Shirts were initially worn as undershirts, however are currently worn regularly as the main garment on the top portion of the body, other than conceivably a brassiere or, seldom, a petticoat (vest). Shirts have likewise turned into a mechanism for self-articulation and publicizing, with any under the sun mix of words, craftsmanship, and photos in plain view.
                A T-shirt regularly stretches out to the midsection. Variations of the T-shirt, like the V-neck, have been created. Hip jump style calls for tall-T shirts which might stretch out down to the knees. A comparable thing is the T-shirt dress or T-dress, a dress-length T-shirt that can be worn without pants. Long T-shirts are likewise some of the time worn by ladies as robes. A 1990s pattern in ladies' clothing included tight-fitting edited T-shirts or tank best short to the point of uncovering the midsection. Another less famous pattern is wearing a short-sleeved T-shirt of a differentiating variety over a long-sleeved T-shirt, which is known as layering. Shirts that are tight to the body are called fitted, custom-made, or child doll T-shirts.
                
                With the ascent of virtual entertainment and video-sharing destinations likewise came various instructional exercises on DIY T-shirt projects. These recordings normally gave directions on the most proficient method to change an old shirt into a new, more trendy structure.",
                "adverts_price" => "9.95",
                "adverts_location" => "Chicago, IL",

                "cf_size" => "M",
                "cf_color" => "Navy",
                "cf_fabric" => "Cotton",
                "cf_pattern" => "Print",

                "_attach" => array(
                    array(
                        "filename" => "t-shirt-2.png",
                        "title" => "Navy t-shirt with a print",
                        "is_featured" => 1
                    ),
                    array(
                        "filename" => "t-shirt-2-flip.png",
                        "title" => "100% cotton"
                    ),
                )
            ),
        );

        return $this->posts;
    }

    public function setup_terms( $user_id ) {
        
        include_once ABSPATH . 'wp-content/plugins/wpadverts/includes/functions.php';
        
        $args = array(
            'hierarchical' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'advert-category')
        );

        register_taxonomy( 'advert_category', 'advert', apply_filters('adverts_register_taxonomy', $args, 'advert_category') );

        
        $delete_terms = get_terms( array(
            "taxonomy" => "advert_category",
            "hide_empty" => 0
        ) );
        
        foreach( $delete_terms as $term ) {
            wp_delete_term( $term->term_id, "advert_category" );
        }
        
        $terms = $this->get_terms();
        
        foreach( $terms as $slug => $term ) {

            if( isset( $term["parent"] ) && !is_null( $term["parent"] ) ) {
                $parent_term_id = $terms[$term["parent"]]["id"];
            } else {
                $parent_term_id = null;
            }

            $tarr = wp_insert_term(
                $term["name"],
                'advert_category',
                array( 'slug' => $slug, 'parent'=> $parent_term_id )
            );

            adverts_taxonomy_update('advert_category', $tarr["term_id"], 'advert_category_icon', $term["icon"]);

            $terms[$slug]["id"] = $tarr["term_id"];
        }
        
        $this->terms = $terms;
    }
    
    public function create_adverts( $user_id, $path ) {

        $terms = $this->get_terms();

        include_once ABSPATH . 'wp-content/plugins/wpadverts/includes/class-adverts.php';
        include_once ABSPATH . 'wp-content/plugins/wpadverts/includes/defaults.php';
        include_once ABSPATH . 'wp-content/plugins/wpadverts/includes/class-post.php';
        include_once ABSPATH . 'wp-content/plugins/wpadverts/includes/class-form.php';
        
        add_image_size( "adverts-upload-thumbnail", 150, 150, false );
        add_image_size( "adverts-list", 310, 310, false );
        
        foreach( $this->get_posts() as $post ) {
            $init = array(
                "post" => array(
                    "ID" => null,
                    "post_type" => "advert",
                    "post_author" => $user_id,
                    "post_date" => current_time( 'mysql' ),
                    "post_date_gmt" => current_time( 'mysql', 1 ),
                    "post_status" => "publish",
                    "guid" => ""
                ),
                "meta" => array(
                    "_expiration_date" => strtotime( current_time('mysql') . " +30 DAYS" )
                )
            );

            if( isset( $post["post_author"] ) && $post["post_author"] ) {
                $init["post"]["post_author"] = $post["post_author"];
            }

            if( isset( $post["post_name"] ) && $post["post_name"] ) {
                $init["post"]["post_name"] = $post["post_name"];
            }

            $post["advert_category"] = $terms[$post["advert_category"]]["id"];

            $form = new \Adverts_Form(\Adverts::instance()->get("form"));
            $form->bind($post);
            $post_id = \Adverts_Post::save($form, null, $init);

            $this->saved[$post_id] = get_post( $post_id );
            
            // Insert attachments
            $zip = new \ZipArchive;
            $res = $zip->open( $path . $this->extract_from );
            $zip->extractTo( $path . $this->extract_to );
            $zip->close();
            
            $files_path = $path . $this->extract_to;
            
            if( !isset( $post["_attach"] ) || empty($post["_attach"])) {
                continue;
            }

            include_once ABSPATH . "/wp-admin/includes/file.php";
            include_once ABSPATH . "/wp-admin/includes/image.php";

            foreach( $post["_attach"] as $file ) {

                $att  = array(
                    "name" => $file["filename"],
                    "tmp_name" => $files_path . $file["filename"]
                );

                $status = wp_handle_upload($att, array('test_form' => false, 'action' => 'adverts_gallery_upload'));
                $filename = $status['file'];
                $filetype = wp_check_filetype( basename( $filename ), null );
                $wp_upload_dir = wp_upload_dir();

                $attachment = array(
                    'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                $attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
                wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filename ) );
                wp_update_post(array(
                    "ID" => $attach_id,
                    "post_excerpt" => (isset($file["title"]) ? $file["title"] : "" ),
                    "post_content" => (isset($file["caption"]) ? $file["caption"] : "" )
                ));

                if( isset($file["is_featured"]) && $file["is_featured"] ) {
                    update_post_meta( $post_id, '_thumbnail_id', $attach_id );
                }

                if( file_exists( $att["tmp_name"] ) ) {
                    unlink( $att["tmp_name"] );
                }
                
            }
        }
        
        /*

        $config = maybe_unserialize( get_option( "adverts_config" ) );
        
        include_once ABSPATH . 'wp-content/plugins/wpadverts/addons/payments/payments.php';
        adext_payments_install();
        
        $config["module"]["payments"] = 1;
        $config["module"]["featured"] = 1;
        $config["module"]["bank-transfer"] = 1;
        $config["module"]["contact-form"] = 1;
        $config["ads_list_default__display"] = "list";
        $config["ads_list_default__switch_views"] = 1;

        update_option( "adverts_config", $config );
        
        $id = wp_insert_post( array( 
            'post_title' => "Premium Refresh",
            'post_content' => "",
            'post_type' => "adverts-renewal"
        ) );

        add_post_meta( $id, 'adverts_visible', '30' );
        add_post_meta( $id, 'adverts_price', '10' );
        
        add_option( "adext_payments_config", array(
            'default_gateway' => 'bank-transfer',
            'default_pricing' => ''
        ) );
        */
    }

    
    public function get_saved_ads() {
        return $this->saved;
    }

}

