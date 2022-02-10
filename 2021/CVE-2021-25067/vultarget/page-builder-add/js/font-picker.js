/*
 * jQuery.fontselect - A font selector for system fonts, local fonts and Google Web Fonts
 *
 * Made by Arjan Haverkamp, https://www.webgear.nl
 * Based on original by Tom Moor, http://tommoor.com
 * Copyright (c) 2011 Tom Moor, 2019-2020 Arjan Haverkamp
 * MIT Licensed
 * @version 1.0 - 2020-02-26
 * @url https://github.com/av01d/fontselect-jquery-plugin
 */

(function($){

      var fontsLoaded = {};

      $.fn.fontselect = function(options) {
            var __bind = function(fn, me) { return function(){ return fn.apply(me, arguments); }; };

            var settings = {
                  style: 'font-select',
                  placeholder: 'Select a font',
                  placeholderSearch: 'Search...',
                  searchable: true,
                  lookahead: 2,
                  googleApi: 'https://fonts.googleapis.com/css?family=',
                  localFontsUrl: '/fonts/',
                  systemFonts: 'Select|Arial|Helvetica+Neue|Courier+New|Times+New+Roman|Comic+Sans+MS|Verdana|Impact'.split('|'),

                  googleFonts: [
                        "ABeeZee:400,italic",
                        "Abel:400",
                        "Abhaya+Libre:400,500,600,700,800",
                        "Abril+Fatface:400",
                        "Aclonica:400",
                        "Acme:400",
                        "Actor:400",
                        "Adamina:400",
                        "Advent+Pro:100,200,300,400,500,600,700",
                        "Aguafina+Script:400",
                        "Akronim:400",
                        "Aladin:400",
                        "Alata:400",
                        "Alatsi:400",
                        "Aldrich:400",
                        "Alef:400,700",
                        "Alegreya:400,italic,500,500italic,700,700italic,800,800italic,900,900italic",
                        "Alegreya+SC:400,italic,500,500italic,700,700italic,800,800italic,900,900italic",
                        "Alegreya+Sans:100,100italic,300,300italic,400,italic,500,500italic,700,700italic,800,800italic,900,900italic",
                        "Alegreya+Sans+SC:100,100italic,300,300italic,400,italic,500,500italic,700,700italic,800,800italic,900,900italic",
                        "Aleo:300,300italic,400,italic,700,700italic",
                        "Alex+Brush:400",
                        "Alfa+Slab+One:400",
                        "Alice:400",
                        "Alike:400",
                        "Alike+Angular:400",
                        "Allan:400,700",
                        "Allerta:400",
                        "Allerta+Stencil:400",
                        "Allura:400",
                        "Almarai:300,400,700,800",
                        "Almendra:400,italic,700,700italic",
                        "Almendra+Display:400",
                        "Almendra+SC:400",
                        "Amarante:400",
                        "Amaranth:400,italic,700,700italic",
                        "Amatic+SC:400,700",
                        "Amethysta:400",
                        "Amiko:400,600,700",
                        "Amiri:400,italic,700,700italic",
                        "Amita:400,700",
                        "Anaheim:400",
                        "Andada:400",
                        "Andika:400",
                        "Angkor:400",
                        "Annie+Use+Your+Telescope:400",
                        "Anonymous+Pro:400,italic,700,700italic",
                        "Antic:400",
                        "Antic+Didone:400",
                        "Antic+Slab:400",
                        "Anton:400",
                        "Arapey:400,italic",
                        "Arbutus:400",
                        "Arbutus+Slab:400",
                        "Architects+Daughter:400",
                        "Archivo:400,italic,500,500italic,600,600italic,700,700italic",
                        "Archivo+Black:400",
                        "Archivo+Narrow:400,italic,500,500italic,600,600italic,700,700italic",
                        "Aref+Ruqaa:400,700",
                        "Arima+Madurai:100,200,300,400,500,700,800,900",
                        "Arimo:400,italic,700,700italic",
                        "Arizonia:400",
                        "Armata:400",
                        "Arsenal:400,italic,700,700italic",
                        "Artifika:400",
                        "Arvo:400,italic,700,700italic",
                        "Arya:400,700",
                        "Asap:400,italic,500,500italic,600,600italic,700,700italic",
                        "Asap+Condensed:400,italic,500,500italic,600,600italic,700,700italic",
                        "Asar:400",
                        "Asset:400",
                        "Assistant:200,300,400,600,700,800",
                        "Astloch:400,700",
                        "Asul:400,700",
                        "Athiti:200,300,400,500,600,700",
                        "Atma:300,400,500,600,700",
                        "Atomic+Age:400",
                        "Aubrey:400",
                        "Audiowide:400",
                        "Autour+One:400",
                        "Average:400",
                        "Average+Sans:400",
                        "Averia+Gruesa+Libre:400",
                        "Averia+Libre:300,300italic,400,italic,700,700italic",
                        "Averia+Sans+Libre:300,300italic,400,italic,700,700italic",
                        "Averia+Serif+Libre:300,300italic,400,italic,700,700italic",
                        "B612:400,italic,700,700italic",
                        "B612+Mono:400,italic,700,700italic",
                        "Bad+Script:400",
                        "Bahiana:400",
                        "Bahianita:400",
                        "Bai+Jamjuree:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Baloo:400",
                        "Baloo+Bhai:400",
                        "Baloo+Bhaijaan:400",
                        "Baloo+Bhaina:400",
                        "Baloo+Chettan:400",
                        "Baloo+Da:400",
                        "Baloo+Paaji:400",
                        "Baloo+Tamma:400",
                        "Baloo+Tammudu:400",
                        "Baloo+Thambi:400",
                        "Balthazar:400",
                        "Bangers:400",
                        "Barlow:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Barlow+Condensed:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Barlow+Semi+Condensed:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Barriecito:400",
                        "Barrio:400",
                        "Basic:400",
                        "Baskervville:400,italic",
                        "Battambang:400,700",
                        "Baumans:400",
                        "Bayon:400",
                        "Be+Vietnam:100,100italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic",
                        "Bebas+Neue:400",
                        "Belgrano:400",
                        "Bellefair:400",
                        "Belleza:400",
                        "BenchNine:300,400,700",
                        "Bentham:400",
                        "Berkshire+Swash:400",
                        "Beth+Ellen:400",
                        "Bevan:400",
                        "Big+Shoulders+Display:100,300,400,500,600,700,800,900",
                        "Big+Shoulders+Text:100,300,400,500,600,700,800,900",
                        "Bigelow+Rules:400",
                        "Bigshot+One:400",
                        "Bilbo:400",
                        "Bilbo+Swash+Caps:400",
                        "BioRhyme:200,300,400,700,800",
                        "BioRhyme+Expanded:200,300,400,700,800",
                        "Biryani:200,300,400,600,700,800,900",
                        "Bitter:400,italic,700",
                        "Black+And+White+Picture:400",
                        "Black+Han+Sans:400",
                        "Black+Ops+One:400",
                        "Blinker:100,200,300,400,600,700,800,900",
                        "Bokor:400",
                        "Bonbon:400",
                        "Boogaloo:400",
                        "Bowlby+One:400",
                        "Bowlby+One+SC:400",
                        "Brawler:400",
                        "Bree+Serif:400",
                        "Bubblegum+Sans:400",
                        "Bubbler+One:400",
                        "Buda:300",
                        "Buenard:400,700",
                        "Bungee:400",
                        "Bungee+Hairline:400",
                        "Bungee+Inline:400",
                        "Bungee+Outline:400",
                        "Bungee+Shade:400",
                        "Butcherman:400",
                        "Butterfly+Kids:400",
                        "Cabin:400,italic,500,500italic,600,600italic,700,700italic",
                        "Cabin+Condensed:400,500,600,700",
                        "Cabin+Sketch:400,700",
                        "Caesar+Dressing:400",
                        "Cagliostro:400",
                        "Cairo:200,300,400,600,700,900",
                        "Calistoga:400",
                        "Calligraffitti:400",
                        "Cambay:400,italic,700,700italic",
                        "Cambo:400",
                        "Candal:400",
                        "Cantarell:400,italic,700,700italic",
                        "Cantata+One:400",
                        "Cantora+One:400",
                        "Capriola:400",
                        "Cardo:400,italic,700",
                        "Carme:400",
                        "Carrois+Gothic:400",
                        "Carrois+Gothic+SC:400",
                        "Carter+One:400",
                        "Catamaran:100,200,300,400,500,600,700,800,900",
                        "Caudex:400,italic,700,700italic",
                        "Caveat:400,700",
                        "Caveat+Brush:400",
                        "Cedarville+Cursive:400",
                        "Ceviche+One:400",
                        "Chakra+Petch:300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Changa:200,300,400,500,600,700,800",
                        "Changa+One:400,italic",
                        "Chango:400",
                        "Charm:400,700",
                        "Charmonman:400,700",
                        "Chathura:100,300,400,700,800",
                        "Chau+Philomene+One:400,italic",
                        "Chela+One:400",
                        "Chelsea+Market:400",
                        "Chenla:400",
                        "Cherry+Cream+Soda:400",
                        "Cherry+Swash:400,700",
                        "Chewy:400",
                        "Chicle:400",
                        "Chilanka:400",
                        "Chivo:300,300italic,400,italic,700,700italic,900,900italic",
                        "Chonburi:400",
                        "Cinzel:400,700,900",
                        "Cinzel+Decorative:400,700,900",
                        "Clicker+Script:400",
                        "Coda:400,800",
                        "Coda+Caption:800",
                        "Codystar:300,400",
                        "Coiny:400",
                        "Combo:400",
                        "Comfortaa:300,400,500,600,700",
                        "Coming+Soon:400",
                        "Concert+One:400",
                        "Condiment:400",
                        "Content:400,700",
                        "Contrail+One:400",
                        "Convergence:400",
                        "Cookie:400",
                        "Copse:400",
                        "Corben:400,700",
                        "Cormorant:300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Cormorant+Garamond:300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Cormorant+Infant:300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Cormorant+SC:300,400,500,600,700",
                        "Cormorant+Unicase:300,400,500,600,700",
                        "Cormorant+Upright:300,400,500,600,700",
                        "Courgette:400",
                        "Courier+Prime:400,italic,700,700italic",
                        "Cousine:400,italic,700,700italic",
                        "Coustard:400,900",
                        "Covered+By+Your+Grace:400",
                        "Crafty+Girls:400",
                        "Creepster:400",
                        "Crete+Round:400,italic",
                        "Crimson+Pro:200,300,400,500,600,700,800,900,200italic,300italic,italic,500italic,600italic,700italic,800italic,900italic",
                        "Crimson+Text:400,italic,600,600italic,700,700italic",
                        "Croissant+One:400",
                        "Crushed:400",
                        "Cuprum:400,italic,700,700italic",
                        "Cute+Font:400",
                        "Cutive:400",
                        "Cutive+Mono:400",
                        "DM+Sans:400,italic,500,500italic,700,700italic",
                        "DM+Serif+Display:400,italic",
                        "DM+Serif+Text:400,italic",
                        "Damion:400",
                        "Dancing+Script:400,500,600,700",
                        "Dangrek:400",
                        "Darker+Grotesque:300,400,500,600,700,800,900",
                        "David+Libre:400,500,700",
                        "Dawning+of+a+New+Day:400",
                        "Days+One:400",
                        "Dekko:400",
                        "Delius:400",
                        "Delius+Swash+Caps:400",
                        "Delius+Unicase:400,700",
                        "Della+Respira:400",
                        "Denk+One:400",
                        "Devonshire:400",
                        "Dhurjati:400",
                        "Didact+Gothic:400",
                        "Diplomata:400",
                        "Diplomata+SC:400",
                        "Do+Hyeon:400",
                        "Dokdo:400",
                        "Domine:400,700",
                        "Donegal+One:400",
                        "Doppio+One:400",
                        "Dorsa:400",
                        "Dosis:200,300,400,500,600,700,800",
                        "Dr+Sugiyama:400",
                        "Duru+Sans:400",
                        "Dynalight:400",
                        "EB+Garamond:400,500,600,700,800,italic,500italic,600italic,700italic,800italic",
                        "Eagle+Lake:400",
                        "East+Sea+Dokdo:400",
                        "Eater:400",
                        "Economica:400,italic,700,700italic",
                        "Eczar:400,500,600,700,800",
                        "El+Messiri:400,500,600,700",
                        "Electrolize:400",
                        "Elsie:400,900",
                        "Elsie+Swash+Caps:400,900",
                        "Emblema+One:400",
                        "Emilys+Candy:400",
                        "Encode+Sans:100,200,300,400,500,600,700,800,900",
                        "Encode+Sans+Condensed:100,200,300,400,500,600,700,800,900",
                        "Encode+Sans+Expanded:100,200,300,400,500,600,700,800,900",
                        "Encode+Sans+Semi+Condensed:100,200,300,400,500,600,700,800,900",
                        "Encode+Sans+Semi+Expanded:100,200,300,400,500,600,700,800,900",
                        "Engagement:400",
                        "Englebert:400",
                        "Enriqueta:400,500,600,700",
                        "Erica+One:400",
                        "Esteban:400",
                        "Euphoria+Script:400",
                        "Ewert:400",
                        "Exo:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Exo+2:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Expletus+Sans:400,italic,500,500italic,600,600italic,700,700italic",
                        "Fahkwang:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Fanwood+Text:400,italic",
                        "Farro:300,400,500,700",
                        "Farsan:400",
                        "Fascinate:400",
                        "Fascinate+Inline:400",
                        "Faster+One:400",
                        "Fasthand:400",
                        "Fauna+One:400",
                        "Faustina:400,500,600,700,italic,500italic,600italic,700italic",
                        "Federant:400",
                        "Federo:400",
                        "Felipa:400",
                        "Fenix:400",
                        "Finger+Paint:400",
                        "Fira+Code:300,400,500,600,700",
                        "Fira+Mono:400,500,700",
                        "Fira+Sans:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Fira+Sans+Condensed:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Fira+Sans+Extra+Condensed:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Fjalla+One:400",
                        "Fjord+One:400",
                        "Flamenco:300,400",
                        "Flavors:400",
                        "Fondamento:400,italic",
                        "Fontdiner+Swanky:400",
                        "Forum:400",
                        "Francois+One:400",
                        "Frank+Ruhl+Libre:300,400,500,700,900",
                        "Freckle+Face:400",
                        "Fredericka+the+Great:400",
                        "Fredoka+One:400",
                        "Freehand:400",
                        "Fresca:400",
                        "Frijole:400",
                        "Fruktur:400",
                        "Fugaz+One:400",
                        "GFS+Didot:400",
                        "GFS+Neohellenic:400,italic,700,700italic",
                        "Gabriela:400",
                        "Gaegu:300,400,700",
                        "Gafata:400",
                        "Galada:400",
                        "Galdeano:400",
                        "Galindo:400",
                        "Gamja+Flower:400",
                        "Gayathri:100,400,700",
                        "Gelasio:400,italic,500,500italic,600,600italic,700,700italic",
                        "Gentium+Basic:400,italic,700,700italic",
                        "Gentium+Book+Basic:400,italic,700,700italic",
                        "Geo:400,italic",
                        "Geostar:400",
                        "Geostar+Fill:400",
                        "Germania+One:400",
                        "Gidugu:400",
                        "Gilda+Display:400",
                        "Girassol:400",
                        "Give+You+Glory:400",
                        "Glass+Antiqua:400",
                        "Glegoo:400,700",
                        "Gloria+Hallelujah:400",
                        "Goblin+One:400",
                        "Gochi+Hand:400",
                        "Gorditas:400,700",
                        "Gothic+A1:100,200,300,400,500,600,700,800,900",
                        "Goudy+Bookletter+1911:400",
                        "Graduate:400",
                        "Grand+Hotel:400",
                        "Gravitas+One:400",
                        "Great+Vibes:400",
                        "Grenze:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Griffy:400",
                        "Gruppo:400",
                        "Gudea:400,italic,700",
                        "Gugi:400",
                        "Gupter:400,500,700",
                        "Gurajada:400",
                        "Habibi:400",
                        "Halant:300,400,500,600,700",
                        "Hammersmith+One:400",
                        "Hanalei:400",
                        "Hanalei+Fill:400",
                        "Handlee:400",
                        "Hanuman:400,700",
                        "Happy+Monkey:400",
                        "Harmattan:400",
                        "Headland+One:400",
                        "Heebo:100,300,400,500,700,800,900",
                        "Henny+Penny:400",
                        "Hepta+Slab:100,200,300,400,500,600,700,800,900",
                        "Herr+Von+Muellerhoff:400",
                        "Hi+Melody:400",
                        "Hind:300,400,500,600,700",
                        "Hind+Guntur:300,400,500,600,700",
                        "Hind+Madurai:300,400,500,600,700",
                        "Hind+Siliguri:300,400,500,600,700",
                        "Hind+Vadodara:300,400,500,600,700",
                        "Holtwood+One+SC:400",
                        "Homemade+Apple:400",
                        "Homenaje:400",
                        "IBM+Plex+Mono:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "IBM+Plex+Sans:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "IBM+Plex+Sans+Condensed:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "IBM+Plex+Serif:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "IM+Fell+DW+Pica:400,italic",
                        "IM+Fell+DW+Pica+SC:400",
                        "IM+Fell+Double+Pica:400,italic",
                        "IM+Fell+Double+Pica+SC:400",
                        "IM+Fell+English:400,italic",
                        "IM+Fell+English+SC:400",
                        "IM+Fell+French+Canon:400,italic",
                        "IM+Fell+French+Canon+SC:400",
                        "IM+Fell+Great+Primer:400,italic",
                        "IM+Fell+Great+Primer+SC:400",
                        "Ibarra+Real+Nova:400,italic,600,600italic,700,700italic",
                        "Iceberg:400",
                        "Iceland:400",
                        "Imprima:400",
                        "Inconsolata:400,700",
                        "Inder:400",
                        "Indie+Flower:400",
                        "Inika:400,700",
                        "Inknut+Antiqua:300,400,500,600,700,800,900",
                        "Inria+Serif:300,300italic,400,italic,700,700italic",
                        "Irish+Grover:400",
                        "Istok+Web:400,italic,700,700italic",
                        "Italiana:400",
                        "Italianno:400",
                        "Itim:400",
                        "Jacques+Francois:400",
                        "Jacques+Francois+Shadow:400",
                        "Jaldi:400,700",
                        "Jim+Nightshade:400",
                        "Jockey+One:400",
                        "Jolly+Lodger:400",
                        "Jomhuria:400",
                        "Jomolhari:400",
                        "Josefin+Sans:100,100italic,300,300italic,400,italic,600,600italic,700,700italic",
                        "Josefin+Slab:100,100italic,300,300italic,400,italic,600,600italic,700,700italic",
                        "Joti+One:400",
                        "Jua:400",
                        "Judson:400,italic,700",
                        "Julee:400",
                        "Julius+Sans+One:400",
                        "Junge:400",
                        "Jura:300,400,500,600,700",
                        "Just+Another+Hand:400",
                        "Just+Me+Again+Down+Here:400",
                        "K2D:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic",
                        "Kadwa:400,700",
                        "Kalam:300,400,700",
                        "Kameron:400,700",
                        "Kanit:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Kantumruy:300,400,700",
                        "Karla:400,italic,700,700italic",
                        "Karma:300,400,500,600,700",
                        "Katibeh:400",
                        "Kaushan+Script:400",
                        "Kavivanar:400",
                        "Kavoon:400",
                        "Kdam+Thmor:400",
                        "Keania+One:400",
                        "Kelly+Slab:400",
                        "Kenia:400",
                        "Khand:300,400,500,600,700",
                        "Khmer:400",
                        "Khula:300,400,600,700,800",
                        "Kirang+Haerang:400",
                        "Kite+One:400",
                        "Knewave:400",
                        "KoHo:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Kodchasan:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Kosugi:400",
                        "Kosugi+Maru:400",
                        "Kotta+One:400",
                        "Koulen:400",
                        "Kranky:400",
                        "Kreon:300,400,500,600,700",
                        "Kristi:400",
                        "Krona+One:400",
                        "Krub:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Kulim+Park:200,200italic,300,300italic,400,italic,600,600italic,700,700italic",
                        "Kumar+One:400",
                        "Kumar+One+Outline:400",
                        "Kurale:400",
                        "La+Belle+Aurore:400",
                        "Lacquer:400",
                        "Laila:300,400,500,600,700",
                        "Lakki+Reddy:400",
                        "Lalezar:400",
                        "Lancelot:400",
                        "Lateef:400",
                        "Lato:100,100italic,300,300italic,400,italic,700,700italic,900,900italic",
                        "League+Script:400",
                        "Leckerli+One:400",
                        "Ledger:400",
                        "Lekton:400,italic,700",
                        "Lemon:400",
                        "Lemonada:300,400,500,600,700",
                        "Lexend+Deca:400",
                        "Lexend+Exa:400",
                        "Lexend+Giga:400",
                        "Lexend+Mega:400",
                        "Lexend+Peta:400",
                        "Lexend+Tera:400",
                        "Lexend+Zetta:400",
                        "Libre+Barcode+128:400",
                        "Libre+Barcode+128+Text:400",
                        "Libre+Barcode+39:400",
                        "Libre+Barcode+39+Extended:400",
                        "Libre+Barcode+39+Extended+Text:400",
                        "Libre+Barcode+39+Text:400",
                        "Libre+Baskerville:400,italic,700",
                        "Libre+Caslon+Display:400",
                        "Libre+Caslon+Text:400,italic,700",
                        "Libre+Franklin:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Life+Savers:400,700,800",
                        "Lilita+One:400",
                        "Lily+Script+One:400",
                        "Limelight:400",
                        "Linden+Hill:400,italic",
                        "Literata:400,500,600,700,italic,500italic,600italic,700italic",
                        "Liu+Jian+Mao+Cao:400",
                        "Livvic:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,900,900italic",
                        "Lobster:400",
                        "Lobster+Two:400,italic,700,700italic",
                        "Londrina+Outline:400",
                        "Londrina+Shadow:400",
                        "Londrina+Sketch:400",
                        "Londrina+Solid:100,300,400,900",
                        "Long+Cang:400",
                        "Lora:400,italic,700,700italic",
                        "Love+Ya+Like+A+Sister:400",
                        "Loved+by+the+King:400",
                        "Lovers+Quarrel:400",
                        "Luckiest+Guy:400",
                        "Lusitana:400,700",
                        "Lustria:400",
                        "M+PLUS+1p:100,300,400,500,700,800,900",
                        "M+PLUS+Rounded+1c:100,300,400,500,700,800,900",
                        "Ma+Shan+Zheng:400",
                        "Macondo:400",
                        "Macondo+Swash+Caps:400",
                        "Mada:200,300,400,500,600,700,900",
                        "Magra:400,700",
                        "Maiden+Orange:400",
                        "Maitree:200,300,400,500,600,700",
                        "Major+Mono+Display:400",
                        "Mako:400",
                        "Mali:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Mallanna:400",
                        "Mandali:400",
                        "Manjari:100,400,700",
                        "Mansalva:400",
                        "Manuale:400,500,600,700,italic,500italic,600italic,700italic",
                        "Marcellus:400",
                        "Marcellus+SC:400",
                        "Marck+Script:400",
                        "Margarine:400",
                        "Markazi+Text:400,500,600,700",
                        "Marko+One:400",
                        "Marmelad:400",
                        "Martel:200,300,400,600,700,800,900",
                        "Martel+Sans:200,300,400,600,700,800,900",
                        "Marvel:400,italic,700,700italic",
                        "Mate:400,italic",
                        "Mate+SC:400",
                        "Maven+Pro:400,500,600,700,800,900",
                        "McLaren:400",
                        "Meddon:400",
                        "MedievalSharp:400",
                        "Medula+One:400",
                        "Meera+Inimai:400",
                        "Megrim:400",
                        "Meie+Script:400",
                        "Merienda:400,700",
                        "Merienda+One:400",
                        "Merriweather:300,300italic,400,italic,700,700italic,900,900italic",
                        "Merriweather+Sans:300,300italic,400,italic,700,700italic,800,800italic",
                        "Metal:400",
                        "Metal+Mania:400",
                        "Metamorphous:400",
                        "Metrophobic:400",
                        "Michroma:400",
                        "Milonga:400",
                        "Miltonian:400",
                        "Miltonian+Tattoo:400",
                        "Mina:400,700",
                        "Miniver:400",
                        "Miriam+Libre:400,700",
                        "Mirza:400,500,600,700",
                        "Miss+Fajardose:400",
                        "Mitr:200,300,400,500,600,700",
                        "Modak:400",
                        "Modern+Antiqua:400",
                        "Mogra:400",
                        "Molengo:400",
                        "Molle:italic",
                        "Monda:400,700",
                        "Monofett:400",
                        "Monoton:400",
                        "Monsieur+La+Doulaise:400",
                        "Montaga:400",
                        "Montez:400",
                        "Montserrat:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Montserrat+Alternates:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Montserrat+Subrayada:400,700",
                        "Moul:400",
                        "Moulpali:400",
                        "Mountains+of+Christmas:400,700",
                        "Mouse+Memoirs:400",
                        "Mr+Bedfort:400",
                        "Mr+Dafoe:400",
                        "Mr+De+Haviland:400",
                        "Mrs+Saint+Delafield:400",
                        "Mrs+Sheppards:400",
                        "Mukta:200,300,400,500,600,700,800",
                        "Mukta+Mahee:200,300,400,500,600,700,800",
                        "Mukta+Malar:200,300,400,500,600,700,800",
                        "Mukta+Vaani:200,300,400,500,600,700,800",
                        "Muli:200,300,400,500,600,700,800,900,200italic,300italic,italic,500italic,600italic,700italic,800italic,900italic",
                        "Mystery+Quest:400",
                        "NTR:400",
                        "Nanum+Brush+Script:400",
                        "Nanum+Gothic:400,700,800",
                        "Nanum+Gothic+Coding:400,700",
                        "Nanum+Myeongjo:400,700,800",
                        "Nanum+Pen+Script:400",
                        "Neucha:400",
                        "Neuton:200,300,400,italic,700,800",
                        "New+Rocker:400",
                        "News+Cycle:400,700",
                        "Niconne:400",
                        "Niramit:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Nixie+One:400",
                        "Nobile:400,italic,500,500italic,700,700italic",
                        "Nokora:400,700",
                        "Norican:400",
                        "Nosifer:400",
                        "Notable:400",
                        "Nothing+You+Could+Do:400",
                        "Noticia+Text:400,italic,700,700italic",
                        "Noto+Sans:400,italic,700,700italic",
                        "Noto+Sans+HK:100,300,400,500,700,900",
                        "Noto+Sans+JP:100,300,400,500,700,900",
                        "Noto+Sans+KR:100,300,400,500,700,900",
                        "Noto+Sans+SC:100,300,400,500,700,900",
                        "Noto+Sans+TC:100,300,400,500,700,900",
                        "Noto+Serif:400,italic,700,700italic",
                        "Noto+Serif+JP:200,300,400,500,600,700,900",
                        "Noto+Serif+KR:200,300,400,500,600,700,900",
                        "Noto+Serif+SC:200,300,400,500,600,700,900",
                        "Noto+Serif+TC:200,300,400,500,600,700,900",
                        "Nova+Cut:400",
                        "Nova+Flat:400",
                        "Nova+Mono:400",
                        "Nova+Oval:400",
                        "Nova+Round:400",
                        "Nova+Script:400",
                        "Nova+Slim:400",
                        "Nova+Square:400",
                        "Numans:400",
                        "Nunito:200,200italic,300,300italic,400,italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Nunito+Sans:200,200italic,300,300italic,400,italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Odibee+Sans:400",
                        "Odor+Mean+Chey:400",
                        "Offside:400",
                        "Old+Standard+TT:400,italic,700",
                        "Oldenburg:400",
                        "Oleo+Script:400,700",
                        "Oleo+Script+Swash+Caps:400,700",
                        "Open+Sans:300,300italic,400,italic,600,600italic,700,700italic,800,800italic",
                        "Open+Sans+Condensed:300,300italic,700",
                        "Oranienbaum:400",
                        "Orbitron:400,500,600,700,800,900",
                        "Oregano:400,italic",
                        "Orienta:400",
                        "Original+Surfer:400",
                        "Oswald:200,300,400,500,600,700",
                        "Over+the+Rainbow:400",
                        "Overlock:400,italic,700,700italic,900,900italic",
                        "Overlock+SC:400",
                        "Overpass:100,100italic,200,200italic,300,300italic,400,italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Overpass+Mono:300,400,600,700",
                        "Ovo:400",
                        "Oxygen:300,400,700",
                        "Oxygen+Mono:400",
                        "PT+Mono:400",
                        "PT+Sans:400,italic,700,700italic",
                        "PT+Sans+Caption:400,700",
                        "PT+Sans+Narrow:400,700",
                        "PT+Serif:400,italic,700,700italic",
                        "PT+Serif+Caption:400,italic",
                        "Pacifico:400",
                        "Padauk:400,700",
                        "Palanquin:100,200,300,400,500,600,700",
                        "Palanquin+Dark:400,500,600,700",
                        "Pangolin:400",
                        "Paprika:400",
                        "Parisienne:400",
                        "Passero+One:400",
                        "Passion+One:400,700,900",
                        "Pathway+Gothic+One:400",
                        "Patrick+Hand:400",
                        "Patrick+Hand+SC:400",
                        "Pattaya:400",
                        "Patua+One:400",
                        "Pavanam:400",
                        "Paytone+One:400",
                        "Peddana:400",
                        "Peralta:400",
                        "Permanent+Marker:400",
                        "Petit+Formal+Script:400",
                        "Petrona:400",
                        "Philosopher:400,italic,700,700italic",
                        "Piedra:400",
                        "Pinyon+Script:400",
                        "Pirata+One:400",
                        "Plaster:400",
                        "Play:400,700",
                        "Playball:400",
                        "Playfair+Display:400,500,600,700,800,900,italic,500italic,600italic,700italic,800italic,900italic",
                        "Playfair+Display+SC:400,italic,700,700italic,900,900italic",
                        "Podkova:400,500,600,700,800",
                        "Poiret+One:400",
                        "Poller+One:400",
                        "Poly:400,italic",
                        "Pompiere:400",
                        "Pontano+Sans:400",
                        "Poor+Story:400",
                        "Poppins:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Port+Lligat+Sans:400",
                        "Port+Lligat+Slab:400",
                        "Pragati+Narrow:400,700",
                        "Prata:400",
                        "Preahvihear:400",
                        "Press+Start+2P:400",
                        "Pridi:200,300,400,500,600,700",
                        "Princess+Sofia:400",
                        "Prociono:400",
                        "Prompt:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Prosto+One:400",
                        "Proza+Libre:400,italic,500,500italic,600,600italic,700,700italic,800,800italic",
                        "Public+Sans:100,200,300,400,500,600,700,800,900,100italic,200italic,300italic,italic,500italic,600italic,700italic,800italic,900italic",
                        "Puritan:400,italic,700,700italic",
                        "Purple+Purse:400",
                        "Quando:400",
                        "Quantico:400,italic,700,700italic",
                        "Quattrocento:400,700",
                        "Quattrocento+Sans:400,italic,700,700italic",
                        "Questrial:400",
                        "Quicksand:300,400,500,600,700",
                        "Quintessential:400",
                        "Qwigley:400",
                        "Racing+Sans+One:400",
                        "Radley:400,italic",
                        "Rajdhani:300,400,500,600,700",
                        "Rakkas:400",
                        "Raleway:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Raleway+Dots:400",
                        "Ramabhadra:400",
                        "Ramaraja:400",
                        "Rambla:400,italic,700,700italic",
                        "Rammetto+One:400",
                        "Ranchers:400",
                        "Rancho:400",
                        "Ranga:400,700",
                        "Rasa:300,400,500,600,700",
                        "Rationale:400",
                        "Ravi+Prakash:400",
                        "Red+Hat+Display:400,italic,500,500italic,700,700italic,900,900italic",
                        "Red+Hat+Text:400,italic,500,500italic,700,700italic",
                        "Redressed:400",
                        "Reem+Kufi:400",
                        "Reenie+Beanie:400",
                        "Revalia:400",
                        "Rhodium+Libre:400",
                        "Ribeye:400",
                        "Ribeye+Marrow:400",
                        "Righteous:400",
                        "Risque:400",
                        "Roboto:100,100italic,300,300italic,400,italic,500,500italic,700,700italic,900,900italic",
                        "Roboto+Condensed:300,300italic,400,italic,700,700italic",
                        "Roboto+Mono:100,100italic,300,300italic,400,italic,500,500italic,700,700italic",
                        "Roboto+Slab:100,200,300,400,500,600,700,800,900",
                        "Rochester:400",
                        "Rock+Salt:400",
                        "Rokkitt:100,200,300,400,500,600,700,800,900",
                        "Romanesco:400",
                        "Ropa+Sans:400,italic",
                        "Rosario:300,400,500,600,700,300italic,italic,500italic,600italic,700italic",
                        "Rosarivo:400,italic",
                        "Rouge+Script:400",
                        "Rozha+One:400",
                        "Rubik:300,300italic,400,italic,500,500italic,700,700italic,900,900italic",
                        "Rubik+Mono+One:400",
                        "Ruda:400,700,900",
                        "Rufina:400,700",
                        "Ruge+Boogie:400",
                        "Ruluko:400",
                        "Rum+Raisin:400",
                        "Ruslan+Display:400",
                        "Russo+One:400",
                        "Ruthie:400",
                        "Rye:400",
                        "Sacramento:400",
                        "Sahitya:400,700",
                        "Sail:400",
                        "Saira:100,200,300,400,500,600,700,800,900",
                        "Saira+Condensed:100,200,300,400,500,600,700,800,900",
                        "Saira+Extra+Condensed:100,200,300,400,500,600,700,800,900",
                        "Saira+Semi+Condensed:100,200,300,400,500,600,700,800,900",
                        "Saira+Stencil+One:400",
                        "Salsa:400",
                        "Sanchez:400,italic",
                        "Sancreek:400",
                        "Sansita:400,italic,700,700italic,800,800italic,900,900italic",
                        "Sarabun:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic",
                        "Sarala:400,700",
                        "Sarina:400",
                        "Sarpanch:400,500,600,700,800,900",
                        "Satisfy:400",
                        "Sawarabi+Gothic:400",
                        "Sawarabi+Mincho:400",
                        "Scada:400,italic,700,700italic",
                        "Scheherazade:400,700",
                        "Schoolbell:400",
                        "Scope+One:400",
                        "Seaweed+Script:400",
                        "Secular+One:400",
                        "Sedgwick+Ave:400",
                        "Sedgwick+Ave+Display:400",
                        "Sevillana:400",
                        "Seymour+One:400",
                        "Shadows+Into+Light:400",
                        "Shadows+Into+Light+Two:400",
                        "Shanti:400",
                        "Share:400,italic,700,700italic",
                        "Share+Tech:400",
                        "Share+Tech+Mono:400",
                        "Shojumaru:400",
                        "Short+Stack:400",
                        "Shrikhand:400",
                        "Siemreap:400",
                        "Sigmar+One:400",
                        "Signika:300,400,600,700",
                        "Signika+Negative:300,400,600,700",
                        "Simonetta:400,italic,900,900italic",
                        "Single+Day:400",
                        "Sintony:400,700",
                        "Sirin+Stencil:400",
                        "Six+Caps:400",
                        "Skranji:400,700",
                        "Slabo+13px:400",
                        "Slabo+27px:400",
                        "Slackey:400",
                        "Smokum:400",
                        "Smythe:400",
                        "Sniglet:400,800",
                        "Snippet:400",
                        "Snowburst+One:400",
                        "Sofadi+One:400",
                        "Sofia:400",
                        "Solway:300,400,500,700,800",
                        "Song+Myung:400",
                        "Sonsie+One:400",
                        "Sorts+Mill+Goudy:400,italic",
                        "Source+Code+Pro:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,900,900italic",
                        "Source+Sans+Pro:200,200italic,300,300italic,400,italic,600,600italic,700,700italic,900,900italic",
                        "Source+Serif+Pro:400,600,700",
                        "Space+Mono:400,italic,700,700italic",
                        "Special+Elite:400",
                        "Spectral:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic",
                        "Spectral+SC:200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic",
                        "Spicy+Rice:400",
                        "Spinnaker:400",
                        "Spirax:400",
                        "Squada+One:400",
                        "Sree+Krushnadevaraya:400",
                        "Sriracha:400",
                        "Srisakdi:400,700",
                        "Staatliches:400",
                        "Stalemate:400",
                        "Stalinist+One:400",
                        "Stardos+Stencil:400,700",
                        "Stint+Ultra+Condensed:400",
                        "Stint+Ultra+Expanded:400",
                        "Stoke:300,400",
                        "Strait:400",
                        "Stylish:400",
                        "Sue+Ellen+Francisco:400",
                        "Suez+One:400",
                        "Sulphur+Point:300,400,700",
                        "Sumana:400,700",
                        "Sunflower:300,500,700",
                        "Sunshiney:400",
                        "Supermercado+One:400",
                        "Sura:400,700",
                        "Suranna:400",
                        "Suravaram:400",
                        "Suwannaphum:400",
                        "Swanky+and+Moo+Moo:400",
                        "Syncopate:400,700",
                        "Tajawal:200,300,400,500,700,800,900",
                        "Tangerine:400,700",
                        "Taprom:400",
                        "Tauri:400",
                        "Taviraj:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Teko:300,400,500,600,700",
                        "Telex:400",
                        "Tenali+Ramakrishna:400",
                        "Tenor+Sans:400",
                        "Text+Me+One:400",
                        "Thasadith:400,italic,700,700italic",
                        "The+Girl+Next+Door:400",
                        "Tienne:400,700,900",
                        "Tillana:400,500,600,700,800",
                        "Timmana:400",
                        "Tinos:400,italic,700,700italic",
                        "Titan+One:400",
                        "Titillium+Web:200,200italic,300,300italic,400,italic,600,600italic,700,700italic,900",
                        "Tomorrow:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Trade+Winds:400",
                        "Trirong:100,100italic,200,200italic,300,300italic,400,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic",
                        "Trocchi:400",
                        "Trochut:400,italic,700",
                        "Trykker:400",
                        "Tulpen+One:400",
                        "Turret+Road:200,300,400,500,700,800",
                        "Ubuntu:300,300italic,400,italic,500,500italic,700,700italic",
                        "Ubuntu+Condensed:400",
                        "Ubuntu+Mono:400,italic,700,700italic",
                        "Ultra:400",
                        "Uncial+Antiqua:400",
                        "Underdog:400",
                        "Unica+One:400",
                        "UnifrakturCook:700",
                        "UnifrakturMaguntia:400",
                        "Unkempt:400,700",
                        "Unlock:400",
                        "Unna:400,italic,700,700italic",
                        "VT323:400",
                        "Vampiro+One:400",
                        "Varela:400",
                        "Varela+Round:400",
                        "Vast+Shadow:400",
                        "Vesper+Libre:400,500,700,900",
                        "Vibes:400",
                        "Vibur:400",
                        "Vidaloka:400",
                        "Viga:400",
                        "Voces:400",
                        "Volkhov:400,italic,700,700italic",
                        "Vollkorn:400,italic,600,600italic,700,700italic,900,900italic",
                        "Vollkorn+SC:400,600,700,900",
                        "Voltaire:400",
                        "Waiting+for+the+Sunrise:400",
                        "Wallpoet:400",
                        "Walter+Turncoat:400",
                        "Warnes:400",
                        "Wellfleet:400",
                        "Wendy+One:400",
                        "Wire+One:400",
                        "Work+Sans:100,200,300,400,500,600,700,800,900",
                        "Yanone+Kaffeesatz:200,300,400,500,600,700",
                        "Yantramanav:100,300,400,500,700,900",
                        "Yatra+One:400",
                        "Yellowtail:400",
                        "Yeon+Sung:400",
                        "Yeseva+One:400",
                        "Yesteryear:400",
                        "Yrsa:300,400,500,600,700",
                        "ZCOOL+KuaiLe:400",
                        "ZCOOL+QingKe+HuangYou:400",
                        "ZCOOL+XiaoWei:400",
                        "Zeyada:400",
                        "Zhi+Mang+Xing:400",
                        "Zilla+Slab:300,300italic,400,italic,500,500italic,600,600italic,700,700italic",
                        "Zilla+Slab+Highlight:400,700"
                  ]
            };

            var Fontselect = (function(){

                  function Fontselect(original, o) {
                        if (!o.systemFonts) { o.systemFonts = []; }
                        if (!o.localFonts) { o.localFonts = []; }
                        if (!o.googleFonts) { o.googleFonts = []; }

                        var googleFonts = [];
                        for (var i = 0; i < o.googleFonts.length; i++) {
                              var item = o.googleFonts[i].split(':'); // Unna:regular,italic,700,700italic
                              var fontName = item[0], fontVariants = item[1] ? item[1].split(',') : [];
                              googleFonts.push(fontName);
                        }
                        o.googleFonts = googleFonts;

                        this.options = o;
                        this.$original = $(original);
                        this.setupHtml();
                        this.getVisibleFonts();
                        this.bindEvents();
                        this.query = '';
                        this.keyActive = false;
                        this.searchBoxHeight = 0;

                        var font = this.$original.val();
                        if (font) {
                              this.updateSelected();
                              this.addFontLink(font);
                        }
                  }

                  Fontselect.prototype = {
                        keyDown: function(e) {

                              function stop(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                              }

                              this.keyActive = true;
                              if (e.keyCode == 27) {// Escape
                                    stop(e);
                                    this.toggleDropdown('hide');
                                    return;
                              }
                              if (e.keyCode == 38) {// Cursor up
                                    stop(e);
                                    var $li = $('li.active', this.$results), $pli = $li.prev('li');
                                    if ($pli.length > 0) {
                                          $li.removeClass('active');
                                          this.$results.scrollTop($pli.addClass('active')[0].offsetTop - this.searchBoxHeight);
                                    }
                                    return;
                              }
                              if (e.keyCode == 40) {// Cursor down
                                    stop(e);
                                    var $li = $('li.active', this.$results), $nli = $li.next('li');
                                    if ($nli.length > 0) {
                                          $li.removeClass('active');
                                          this.$results.scrollTop($nli.addClass('active')[0].offsetTop - this.searchBoxHeight);
                                    }
                                    return;
                              }
                              if (e.keyCode == 13) {// Enter
                                    stop(e);
                                    $('li.active', this.$results).trigger('click');
                                    return;
                              }
                              this.query += String.fromCharCode(e.keyCode).toLowerCase();
                              var $found = $("li[data-query^='"+ this.query +"']").first();
                              if ($found.length > 0) {
                                    $('li.active', this.$results).removeClass('active');
                                    this.$results.scrollTop($found.addClass('active')[0].offsetTop);
                              }
                        },

                        keyUp: function(e) {
                              this.keyActive = false;
                        },

                        bindEvents: function() {
                              var self = this;

                              $('li', this.$results)
                              .on('click',__bind(this.selectFont, this))
                              .on('mouseover',__bind(this.activateFont, this));

                              this.$select.on('click',__bind(function() { self.toggleDropdown('show') }, this));

                              // Call like so: $("input[name='ffSelect']").trigger('setFont', [fontFamily, fontWeight]);
                              this.$original.on('setFont', function(evt, fontFamily, fontWeight) {
                                    fontWeight = fontWeight || 400;

                                    var fontSpec = fontFamily.replace(/ /g, '+') + ':' + fontWeight;

                                    var $li = $("li[data-value='"+ fontSpec +"']", self.$results);
                                    if ($li.length == 0) {
                                          fontSpec = fontFamily.replace(/ /g, '+');
                                    }
                                    //console.log(fontSpec);
                                    $li = $("li[data-value='"+ fontSpec +"']", self.$results);
                                    $('li.active', self.$results).removeClass('active');
                                    $li.addClass('active');

                                    self.$original.val(fontSpec);
                                    self.updateSelected();
                                    self.addFontLink($li.data('value'));
                                    //$li.trigger('click'); // Removed 2019-10-16
                              });
                              this.$original.on('change', function() {
                                    self.updateSelected();
                                    self.addFontLink($('li.active', self.$results).data('value'));
                              });

                              if (this.options.searchable) {
                                    this.$input.on('keyup', function() {
                                          var q = this.value.toLowerCase();
                                          // Hide options that don't match query:
                                          $('li', self.$results).each(function() {
                                                if ($(this).text().toLowerCase().indexOf(q) == -1) {
                                                      $(this).hide();
                                                }
                                                else {
                                                      $(this).show();
                                                }
                                          })
                                    })
                              }

                              $(document).on('click', function(e) {
                                    if ($(e.target).closest('.'+self.options.style).length === 0) {
                                          self.toggleDropdown('hide');
                                    }
                              });
                        },

                        toggleDropdown: function(hideShow) {
                              if (hideShow === 'hide') {
                                    // Make inactive
                                    this.$element.off('keydown keyup');
                                    this.query = '';
                                    this.keyActive = false;
                                    this.$element.removeClass('font-select-active');
                                    this.$drop.hide();
                                    clearInterval(this.visibleInterval);
                              } else {
                                    // Make active
                                    this.$element.on('keydown', __bind(this.keyDown, this));
                                    this.$element.on('keyup', __bind(this.keyUp, this));
                                    this.$element.addClass('font-select-active');
                                    this.$drop.show();

                                    this.visibleInterval = setInterval(__bind(this.getVisibleFonts, this), 500);
                                    this.searchBoxHeight = this.$search.outerHeight();
                                    this.moveToSelected();

                                    /*
                                    if (this.options.searchable) {
                                          // Focus search box
                                          $this.$input.focus();
                                    }
                                    */
                              }
                        },

                        selectFont: function() {
                              var font = $('li.active', this.$results).data('value');
                              this.$original.val(font).change();
                              this.updateSelected();
                              this.toggleDropdown('hide'); // Hide dropdown
                        },

                        moveToSelected: function() {
                              var font = this.$original.val().replace(/ /g, '+');
                              var $li = font ? $("li[data-value='"+ font +"']", this.$results) : $li = $('li', this.$results).first();
                              this.$results.scrollTop($li.addClass('active')[0].offsetTop - 90);
                        },

                        activateFont: function(e) {
                              if (this.keyActive) { return; }
                              $('li.active', this.$results).removeClass('active');
                              $(e.target).addClass('active');
                        },

                        updateSelected: function() {
                              var font = this.$original.val();
                              $('span', this.$element).text(this.toReadable(font)).css(this.toStyle(font));
                        },

                        setupHtml: function() {
                              this.$original.hide();
                              this.$element = $('<div>', {'class': this.options.style});
                              this.$select = $('<span tabindex="0">' + this.options.placeholder + '</span>');
                              this.$search = $('<div>', {'class': 'fs-search'});
                              this.$input = $('<input>', {type:'text'});
                              if (this.options.placeholderSearch) {
                                    this.$input.attr('placeholder', this.options.placeholderSearch);
                              }
                              this.$search.append(this.$input);
                              this.$drop = $('<div>', {'class': 'fs-drop'});
                              this.$results = $('<ul>', {'class': 'fs-results'});
                              this.$original.after(this.$element.append(this.$select, this.$drop));
                              this.options.searchable && this.$drop.append(this.$search);
                              this.$drop.append(this.$results.append(this.fontsAsHtml())).hide();
                        },

                        fontsAsHtml: function() {
                              var i, r, s, style, h = '';
                              var localFonts = this.options.localFonts;
                              var systemFonts = this.options.systemFonts;
                              var googleFonts = this.options.googleFonts;

                              for (i = 0; i < localFonts.length; i++){
                                    r = this.toReadable(localFonts[i]);
                                    s = this.toStyle(localFonts[i]);
                                    style = 'font-family:' + s['font-family'];
                                    if (googleFonts.length > 0 && i == localFonts.length-1) {
                                          style += ';border-bottom:1px solid #444'; // Separator after last local font
                                    }
                                    h += '<li data-value="'+ localFonts[i] +'" data-query="' + localFonts[i].toLowerCase() + '" style="' + style + '">' + r + '</li>';
                              }

                              for (i = 0; i < systemFonts.length; i++){
                                    r = this.toReadable(systemFonts[i]);
                                    s = this.toStyle(systemFonts[i]);

                                    style = 'font-family:' + s['font-family'];
                                    if ((localFonts.length > 0 || googleFonts.length > 0) && i == systemFonts.length-1) {
                                          style += ';border-bottom:1px solid #444'; // Separator after last system font
                                    }
                                    

                                    if (r == 'Select') {
                                          h += '<li data-value=" " data-query=" ">' + r + '</li>';
                                    }else{
                                          h += '<li data-value="'+ systemFonts[i] +'" data-query="' + systemFonts[i].toLowerCase() + '" style="' + style + '">' + r + '</li>';
                                    }
                              }

                              for (i = 0; i < googleFonts.length; i++){
                                    r = this.toReadable(googleFonts[i]);
                                    s = this.toStyle(googleFonts[i]);
                                    style = 'font-family:' + s['font-family'] + ';font-weight:' + s['font-weight'] + ';font-style:' + s['font-style'];
                                    h += '<li data-value="'+ googleFonts[i] +'" data-query="' + googleFonts[i].toLowerCase() + '" style="' + style + '">' + r + '</li>';
                              }

                              return h;
                        },

                        toReadable: function(font) {
                              return font.replace(/[\+|:]/g, ' ').replace(/(\d+)italic/, '$1 italic');
                        },

                        toStyle: function(font) {
                              var t = font.split(':'), italic = false;
                              if (t[1] && /italic/.test(t[1])) {
                                    italic = true;
                                    t[1] = t[1].replace('italic','');
                              }

                              return {'font-family':"'"+this.toReadable(t[0])+"'", 'font-weight': (t[1] || 400), 'font-style': italic?'italic':'normal'};
                        },

                        getVisibleFonts: function() {
                              if(this.$results.is(':hidden')) { return; }

                              var fs = this;
                              var top = this.$results.scrollTop();
                              var bottom = top + this.$results.height();

                              if (this.options.lookahead){
                                    var li = $('li', this.$results).first().height();
                                    bottom += li * this.options.lookahead;
                              }

                              $('li:visible', this.$results).each(function(){
                                    var ft = $(this).position().top+top;
                                    var fb = ft + $(this).height();

                                    if ((fb >= top) && (ft <= bottom)){
                                          fs.addFontLink($(this).data('value'));
                                    }
                              });
                        },

                        addFontLink: function(font) {
                              if (fontsLoaded[font]) { return; }
                              fontsLoaded[font] = true;

                              if (this.options.googleFonts.indexOf(font) > -1) {
                                    $('link:last').after('<link href="' + this.options.googleApi + font + '" rel="stylesheet" type="text/css">');
                              }
                              else if (this.options.localFonts.indexOf(font) > -1) {
                                    font = this.toReadable(font);
                                    $('head').append("<style> @font-face { font-family:'" + font + "'; font-style:normal; font-weight:400; src:local('" + font + "'), url('" + this.options.localFontsUrl + font + ".woff') format('woff'); } </style>");
                              }
                              // System fonts need not be loaded!
                        }
                  }; // End prototype

                  return Fontselect;
            })();

            return this.each(function() {
                  // If options exist, merge them
                  if (options) { $.extend(settings, options); }

                  return new Fontselect(this, settings);
            });
      };
})(jQuery);