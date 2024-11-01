<?php

/**
 * Shared utility class.
 *
 * This class defines all helper functions to be shared and used throughout the plugin.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Utilities {
    /**
     * Static function which returns the default list of stop words.
     *
     * @since    0.1.0
     *
     * @param    string $option_name The case to be used for the conversion.
     *
     * @return   array       The list of stop words.
     */
    public static function get_option( $option_name ) {
        $options = get_option( Supasearch::get_option_name() );

        return is_array( $options ) && isset( $options[$option_name] ) ? $options[$option_name] : null;
    }

    /**
     * Static function which converts and url friendly name to a label.
     *
     * @since    0.1.0
     *
     * @param    string $name The name to be converted to a label.
     * @param    string $case The case to be used for the conversion.
     *
     * @return   string       The convert name.
     */
    public static function get_label_from_name( $name, $case = 'ucwords' ) {
        return function_exists( $case ) ? $case( str_replace( '-', ' ', $name ) ) : $name;
    }

    /**
     * Static function which returns the default list of expletive words.
     *
     * @since    0.1.0
     *
     * @return   array       The list of expletive words.
     */
    public static function get_expletive_words() {
        $expletive_words = self::get_option( 'expletive_words' );

        return $expletive_words !== null ? explode( ',', $expletive_words ) : array( '4r5e', '50 yard cunt punt†††', '5h1t', '5hit', 'a_s_s', 'a2m', 'a55', 'adult', 'amateur', 'anal', 'anal impaler†††', 'anal leakage†††', 'anilingus', 'anus', 'ar5e', 'arrse', 'arse', 'arsehole', 'ass', 'ass fuck†††', 'asses', 'assfucker', 'ass-fucker', 'assfukka', 'asshole', 'asshole', 'assholes', 'assmucus†††', 'assmunch', 'asswhole', 'autoerotic', 'b!tch', 'b00bs', 'b17ch', 'b1tch', 'ballbag', 'ballsack', 'bang (one\'s) box†††', 'bangbros', 'bareback', 'bastard', 'beastial', 'beastiality', 'beef curtain†††', 'bellend', 'bestial', 'bestiality', 'bi + ch', 'biatch', 'bimbos', 'birdlock', 'bitch', 'bitch tit†††', 'bitcher', 'bitchers', 'bitches', 'bitchin', 'bitching', 'bloody', 'blow job', 'blow me†††', 'blow mud†††', 'blowjob', 'blowjobs', 'blue waffle†††', 'blumpkin†††', 'boiolas', 'bollock', 'bollok', 'boner', 'boob', 'boobs', 'booobs', 'boooobs', 'booooobs', 'booooooobs', 'breasts', 'buceta', 'bugger', 'bum', 'bunny fucker', 'bust a load†††', 'busty', 'butt', 'butt fuck†††', 'butthole', 'buttmuch', 'buttplug', 'c0ck', 'c0cksucker', 'carpet muncher', 'carpetmuncher', 'cawk', 'chink', 'choade†††', 'chota bags†††', 'cipa', 'cl1t', 'clit', 'clit licker†††', 'clitoris', 'clits', 'clitty litter†††', 'clusterfuck', 'cnut', 'cock', 'cock pocket†††', 'cock snot†††', 'cockface', 'cockhead', 'cockmunch', 'cockmuncher', 'cocks', 'cocksuck ', 'cocksucked ', 'cocksucker', 'cock - sucker', 'cocksucking', 'cocksucks ', 'cocksuka', 'cocksukka', 'cok', 'cokmuncher', 'coksucka', 'coon', 'cop some wood†††', 'cornhole†††', 'corp whore†††', 'cox', 'cum', 'cum chugger†††', 'cum dumpster†††', 'cum freak†††', 'cum guzzler†††', 'cumdump†††', 'cummer', 'cumming', 'cums', 'cumshot', 'cunilingus', 'cunillingus', 'cunnilingus', 'cunt', 'cunt hair†††', 'cuntbag†††', 'cuntlick ', 'cuntlicker ', 'cuntlicking ', 'cunts', 'cuntsicle†††', 'cunt - struck†††', 'cut rope†††', 'cyalis', 'cyberfuc', 'cyberfuck ', 'cyberfucked ', 'cyberfucker', 'cyberfuckers', 'cyberfucking ', 'd1ck', 'damn', 'dick', 'dick hole†††', 'dick shy†††', 'dickhead', 'dildo', 'dildos', 'dink', 'dinks', 'dirsa', 'dirty Sanchez†††', 'dlck', 'dog - fucker', 'doggie style', 'doggiestyle', 'doggin', 'dogging', 'donkeyribber', 'doosh', 'duche', 'dyke', 'eat a dick†††', 'eat hair pie†††', 'ejaculate', 'ejaculated', 'ejaculates ', 'ejaculating ', 'ejaculatings', 'ejaculation', 'ejakulate', 'erotic', 'f u c k', 'f u c k e r', 'f_u_c_k', 'f4nny', 'facial†††', 'fag', 'fagging', 'faggitt', 'faggot', 'faggs', 'fagot', 'fagots', 'fags', 'fanny', 'fannyflaps', 'fannyfucker', 'fanyy', 'fatass', 'fcuk', 'fcuker', 'fcuking', 'feck', 'fecker', 'felching', 'fellate', 'fellatio', 'fingerfuck ', 'fingerfucked ', 'fingerfucker ', 'fingerfuckers', 'fingerfucking ', 'fingerfucks ', 'fist fuck†††', 'fistfuck', 'fistfucked ', 'fistfucker ', 'fistfuckers ', 'fistfucking ', 'fistfuckings ', 'fistfucks ', 'flange', 'flog the log†††', 'fook', 'fooker', 'fuck hole†††', 'fuck puppet†††', 'fuck trophy†††', 'fuck yo mama†††', 'fuck†††', 'fucka', 'fuck - ass†††', 'fuck - bitch†††', 'fucked', 'fucker', 'fuckers', 'fuckhead', 'fuckheads', 'fuckin', 'fucking', 'fuckings', 'fuckingshitmotherfucker', 'fuckme ', 'fuckmeat†††', 'fucks', 'fucktoy†††', 'fuckwhit', 'fuckwit', 'fudge packer', 'fudgepacker', 'fuk', 'fuker', 'fukker', 'fukkin', 'fuks', 'fukwhit', 'fukwit', 'fux', 'fux0r', 'gangbang', 'gangbang†††', 'gang - bang†††', 'gangbanged ', 'gangbangs ', 'gassy ass†††', 'gaylord', 'gaysex', 'goatse', 'god', 'god damn', 'god - dam', 'goddamn', 'goddamned', 'god - damned', 'ham flap†††', 'hardcoresex ', 'hell', 'heshe', 'hoar', 'hoare', 'hoer', 'homo', 'homoerotic', 'hore', 'horniest', 'horny', 'hotsex', 'how to kill', 'how to murdep', 'jackoff', 'jack - off ', 'jap', 'jerk', 'jerk - off ', 'jism', 'jiz ', 'jizm ', 'jizz', 'kawk', 'kinky Jesus†††', 'knob', 'knob end', 'knobead', 'knobed', 'knobend', 'knobend', 'knobhead', 'knobjocky', 'knobjokey', 'kock', 'kondum', 'kondums', 'kum', 'kummer', 'kumming', 'kums', 'kunilingus', 'kwif†††', 'l3i + ch', 'l3itch', 'labia', 'LEN', 'lmao', 'lmfao', 'lmfao', 'lust', 'lusting', 'm0f0', 'm0fo', 'm45terbate', 'ma5terb8', 'ma5terbate', 'mafugly†††', 'masochist', 'masterb8', 'masterbat * ', 'masterbat3', 'masterbate', 'master - bate', 'masterbation', 'masterbations', 'masturbate', 'mof0', 'mofo', 'mo - fo', 'mothafuck', 'mothafucka', 'mothafuckas', 'mothafuckaz', 'mothafucked ', 'mothafucker', 'mothafuckers', 'mothafuckin', 'mothafucking ', 'mothafuckings', 'mothafucks', 'mother fucker', 'mother fucker†††', 'motherfuck', 'motherfucked', 'motherfucker', 'motherfuckers', 'motherfuckin', 'motherfucking', 'motherfuckings', 'motherfuckka', 'motherfucks', 'muff', 'muff puff†††', 'mutha', 'muthafecker', 'muthafuckker', 'muther', 'mutherfucker', 'n1gga', 'n1gger', 'nazi', 'need the dick†††', 'nigg3r', 'nigg4h', 'nigga', 'niggah', 'niggas', 'niggaz', 'nigger', 'niggers ', 'nob', 'nob jokey', 'nobhead', 'nobjocky', 'nobjokey', 'numbnuts', 'nut butter†††', 'nutsack', 'omg', 'orgasim ', 'orgasims ', 'orgasm', 'orgasms ', 'p0rn', 'pawn', 'pecker', 'penis', 'penisfucker', 'phonesex', 'phuck', 'phuk', 'phuked', 'phuking', 'phukked', 'phukking', 'phuks', 'phuq', 'pigfucker', 'pimpis', 'piss', 'pissed', 'pisser', 'pissers', 'pisses ', 'pissflaps', 'pissin ', 'pissing', 'pissoff ', 'poop', 'porn', 'porno', 'pornography', 'pornos', 'prick', 'pricks ', 'pron', 'pube', 'pusse', 'pussi', 'pussies', 'pussy', 'pussy fart†††', 'pussy palace†††', 'pussys ', 'queaf†††', 'queer', 'rectum', 'retard', 'rimjaw', 'rimming', 's hit', 's . o . b . ', 's_h_i_t', 'sadism', 'sadist', 'sandbar†††', 'sausage queen†††', 'schlong', 'screwing', 'scroat', 'scrote', 'scrotum', 'semen', 'sex', 'sh!+', 'sh!t', 'sh1t', 'shag', 'shagger', 'shaggin', 'shagging', 'shemale', 'shi + ', 'shit', 'shit fucker†††', 'shitdick', 'shite', 'shited', 'shitey', 'shitfuck', 'shitfull', 'shithead', 'shiting', 'shitings', 'shits', 'shitted', 'shitter', 'shitters ', 'shitting', 'shittings', 'shitty ', 'skank', 'slope†††', 'slut', 'slut bucket†††', 'sluts', 'smegma', 'smut', 'snatch', 'son - of - a - bitch', 'spac', 'spunk', 't1tt1e5', 't1tties', 'teets', 'teez', 'testical', 'testicle', 'tit', 'tit wank†††', 'titfuck', 'tits', 'titt', 'tittie5', 'tittiefucker', 'titties', 'tittyfuck', 'tittywank', 'titwank', 'tosser', 'turd', 'tw4t', 'twat', 'twathead', 'twatty', 'twunt', 'twunter', 'v14gra', 'v1gra', 'vagina', 'viagra', 'vulva', 'w00se', 'wang', 'wank', 'wanker', 'wanky', 'whoar', 'whore', 'willies', 'willy', 'wtf', 'xrated', 'xxx' );
    }

    /**
     * Static function which returns the default list of stop words.
     *
     * @since    0.1.0
     *
     * @return   array       The list of stop words.
     */
    public static function get_stop_words() {
        $stop_words = self::get_option( 'stop_words' );

        return $stop_words !== null ? explode( ',', $stop_words ) : array( 'i', 'the', 'to', 'and', 'a', 'of', 'http', 'com', 'it', 'you', 'that', 'in', 's', 'my', 'is', 'was', 'for', 'www', 't', 'he', 'on', 'me', 'with', 'but', 'so', 'have', 'this', 'be', 'at', 'his', 'we', 'as', 'all', 'm', 'like', 'what', 'img', 'nbsp', 'are', 'out', 'up', 'she', 'do', 'her', 'they', 'or', 'if', 'by', 'had', 'one', 'your', 'about', 'can', 'from', 'there', 'get', 'when', 'him', 'no', 'now', 'would', 'then', 'don', 'will', 'been', 'some', 'an', 'who', 'how', 'going', 'them', 'got', 'well', 'am', 'were', 'because', 've', 'want', 'much', 'see', 'll', 'd', 'has', 'over', 're', 'into', 'only', 'which', 'other', 'too', 'here', 'could', 'even', 'than', 'off', 'did', 'their', 'amp', 'also', 'should', 'these', 'where', 'within' );
    }

    /**
     * Static function which returns the minimum match percentage for the did you mean suggestion.
     *
     * @since    0.1.0
     *
     * @return   float       The did you mean minimum match value.
     */
    public static function get_min_match_percentage() {
        $min_match_percentage = self::get_option( 'min_match_percentage' );

        return $min_match_percentage !== null ? $min_match_percentage : 0.25;
    }

    /**
     * Static function which returns the how close by percentage a previous query has to be for it to be return for the
     * did you mean suggestion.
     *
     * @since    0.1.0
     *
     * @return   float       The did you mean minimum match value.
     */
    public static function get_previous_query_closeness() {
        $previous_query_closeness = self::get_option( 'previous_query_closeness' );

        return $previous_query_closeness !== null ? $previous_query_closeness : 0.8;
    }

    /**
     * Static function which returns the minimum number of times a previous query needs to have been searched for it to
     * become a popular choice for the did you mean suggestion.
     *
     * @since    0.1.0
     *
     * @return   float       The did you mean minimum match value.
     */
    public static function get_min_number_hits() {
        $min_number_hits = self::get_option( 'min_number_hits' );

        return $min_number_hits !== null ? $min_number_hits : 10;
    }

    /**
     * Static function which returns a partial template.
     *
     * @since    0.1.0
     *
     * @param    string $partial  The name of the partial to load.
     * @param    array  $data     An array of variables to be used by the partial.
     * @param    string $location Determines if the partial is to be loaded from admin or public.
     *
     * @return   string             The partial contents to be rendered.
     */
    public static function get_partial( $partial, $data, $location = 'public' ) {
        // Start output buffer
        ob_start();

        // Generate file path from parameters
        $filePath = "{$location}/partials/" . Supasearch::get_plugin_name() . "-{$location}-{$partial}.php";

        // Set partial variables
        extract( $data );

        // Include partial
        include $location !== 'admin' && ( $file = locate_template( 'plugins / ' . Supasearch::get_plugin_name() . ' / ' . $filePath ) ) !== '' ? $file : plugin_dir_path( dirname( __FILE__ ) ) . $filePath;

        // Store partial contents
        $html = ob_get_contents();

        // Stop output buffer
        ob_end_clean();

        // Return contents
        return $html;
    }
}