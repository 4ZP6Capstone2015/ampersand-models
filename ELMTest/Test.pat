{- El Metodo 2 -------------------------------------------}
--[Assets]-------------------------------------------------
PATTERN "Assets" -- WIJZIGER: rieks.joosten@tno.nl
PURPOSE PATTERN "Assets" IN DUTCH
{+Om te kunnen waarborgen dat bedrijfsfuncties in voldoende mate goed worden uitgevoerd, introduceren we 'bedrijfsmiddelen' of 'assets'. -}

CONCEPT Asset "(de specificatie voor) een specifieke implementatie (inrichting) van een (bedrijfs)functie"
PURPOSE CONCEPT Asset IN DUTCH
{+Bedrijfsfuncties kunnen vaak op verschillende manieren en met verschillende middelen worden uitgevoerd. Om deze uitvoeringswijzen van elkaar te onderscheiden voeren we de term asset (bedrijfsmiddel) in. Van een asset wordt de uitvoeringswijze enerzijds bepaald door een verzameling verplichtingen (specificaties, requirements) waaraan de uitvoeringswijze moet voldoen. Anderzijds wordt de uitvoeringswijze (mede) bepaald door een verzameling aannames (verwachtingen ten aanzien van andere assets) op basis waarvan het eigen werk goed kan worden ingericht.
De verantwoordelijkheid dat het bedrijfsmiddel volgens zijn verplichtingen functioneert is belegd bij een persoon die we ‘eigenaar van de asset’ noemen.-}
-- GEN Asset ISA Contents --!Hiermee introduceren we versie-beheer

CONCEPT Persoon "een mens van vlees en bloed"
PURPOSE CONCEPT Persoon IN DUTCH
{+Om verantwoordelijkheden te kunnen beleggen moeten we kunnen spreken over mensen van vlees en bloed.-}

eigenaar :: Asset -> Persoon PRAGMA "De eindverantwoordelijkheid voor " " is toegekend aan "
PURPOSE RELATION eigenaar IN DUTCH
{+Om te kunnen waarborgen dat elke asset zijn functie in voldoende mate vervult, kennen we aan elke asset een persoon toe die ervoor verantwoordelijk is dat de asset aan elk van zijn verplicthingen voldoet.-}

operationeel :: Asset * Asset [PROP] PRAGMA "De eigenaar van " " heeft besloten dat " " operationeel functioneert"
PURPOSE RELATION operationeel IN DUTCH
{+Een asset die operationeel functioneert mag niet zonder meer gewijzigd worden omdat dit operationele risico's met zich mee kan brengen. Daarom is van elke asset bekend of deze al dan niet operationeel is.-}

RULE "operationele assets": operationeel |- kanFunctioneren /\ acceptabelRestrisico
PHRASE "Zolang een asset operationeel is moet hij kunnen functioneren."
PURPOSE RULE "operationele assets" IN DUTCH
{+Gedurende het operationele leven van een asset moet hij kunnen blijven functioneren, hetgeen steeds moet kunnen worden vastgesteld. Ook moet het restrisico van de asset op een acceptabel niveau liggen, hetgeen ook steeds moet kunnen worden vastegesteld.-}

kanFunctioneren :: Asset * Asset [PROP] PRAGMA "Van " " is vastgesteld dat deze goed kan functioneren"
PURPOSE RELATION kanFunctioneren IN DUTCH
{+Om te waarborgen dat een asset (goed) kan functioneren moet dit kunnen worden vastgesteld. Van elke asset waarvan dit is vastgesteld zeggen we dat die de eigenschap 'kanFunctioneren' heeft.-}

acceptabelRestrisico :: Asset * Asset [PROP] PRAGMA "De eigenaar van " " heeft besloten dat alle risico's met betrekking tot " " in hun gezamenlijkheid acceptabel zijn".
PURPOSE RELATION acceptabelRestrisico IN DUTCH
{+Van elke asset moet diens eigenaar expliciet kunnen beslissen dat het restrisico van die asset acceptabel is. Onder het 'restrisico' van een asset verstaan we alle risico's zoals die voor de geinventariseerde verplichtingen zijn ingeschat, alsmede alle risico's van de niet geinventariseerde verplichtingen. Omdat van de niet geinventariseerde verplichtingen de risico's onbekend zijn, impliceert het nemen van voornoemd besluit dat de eigenaar deze onbekende risico's als voldoende laag inschat.
Als deze eigenaar dit besluit heeft genomen, dan zeggen we dat de asset de eigenschap 'acceptabelRestrisico' heeft.-}

ENDPATTERN
--[Asset Inrichting]----------------------------------------
PATTERN "Asset Inrichting" 
PURPOSE PATTERN "Asset Inrichting" IN DUTCH
{+Om aan de verplichtingen van een asset te kunnen voldoen moet werk worden verzet, dat wordt gespecificeerd door een verzameling criteria (verwachtingen) waaraan alleen is voldaan als het beoogde resultaat van dat werk is gehaald. Een verplichting van een asset heet ‘afgedekt’ door zijn verwachtingen als de asset eigenaar heeft besloten dat hij aan de verplichting heeft voldaan als alle bijbehorende verwachtingen zijn uitgekomen. Een asset heet ‘volledig ingericht’ als alle verplichtingen van de asset zijn afgedekt-}

RULE "asset kan functioneren": kanFunctioneren = -verplichtingVan~ ! (dektAf~; isVolledig; V)
PHRASE "Een asset kan alleen functioneren als elk van zijn verplichtingen een afdekking heeft die volledig is."
PURPOSE RULE "asset kan functioneren" IN DUTCH
{+Om een asset (hernieuwd) te kunnen inrichten moet van alle verplichtingen kunnen worden vastgesteld dat ze compleet zijn afgedekt.-}

CONCEPT Verplichting "een regel (requirement) die door een asset waar gemaakt c.q. waar gehouden moet worden en waarvoor de asset eigenaar verantwoordelijk (accountable) is"

verplichtingVan :: Verplichting -> Asset PRAGMA "" " moet binnen " " zijn afgedekt".
PURPOSE RELATION verplichtingVan IN DUTCH
{+De functie van een asset wordt gespecificeerd door verplichtingen (requirements) die door de asset moeten worden waargemaakt c.q. nageleefd. Om te kunnen waarborgen dat elke asset zijn functie in voldoende mate vervult, moeten we al deze verplichtingen kennen.-}

CONCEPT Verwachting "een (toetsbare) aanname die, om een zekere verplichting te kunnen voldoen, waar zou moeten zijn"
PURPOSE CONCEPT Verwachting IN DUTCH
{+Om vast te kunnen stellen of aan een zekere verplichting van een asset kan worden voldaan, moet de asset eigenaar in staat worden gesteld toetsbare criteria te postuleren waarvan hij aanneemt dat ze waar zullen zijn zodat hij op basis daarvan dit vaststellingsbesluit kan nemen. Dat zulke aannames niet per se waar hoeven te zijn maakt dat we hiervoor liever het woord 'verwachting' gebruiken.-}

verwachtingVan :: Verwachting -> Asset PRAGMA "" " is een (toetsbare) aanname  waarvan (de eigenaar van) " " verwacht dat er aan zal worden voldaan".
PURPOSE RELATION verwachtingVan IN DUTCH
{+Om een verplichting waar te kunnen maken wordt binnen de asset gespecificeerd wat daarvoor nodig is. Zo'n specificatie bestaat uit criteria die we 'verwachtingen' van de asset noemen.-}

ENDPATTERN
--[Afdekken van verplichtingen]-----------------------------
PATTERN Afdekkingen
PURPOSE PATTERN Afdekkingen IN DUTCH
{+Om inrichtings- en risico management behapbaar te kunnen houden is het nodig om per verplichting van een asset na te kunnen gaan op basis waarvan die asset deze verplicthing gaat waarmaken.-}

CONCEPT Afdekking "een verzameling van verwachtingen, horend bij precies een verplichting, die bedoeld zijn deze verplichting af te dekken"
PURPOSE CONCEPT Afdekking IN DUTCH
{+Of een asset aan een specifieke verplichting kan voldoen moet kunnen worden vastgesteld. Daartoe kennen we een aantal verwachtingen toe aan deze verplichting. Het samenstel van de verplichting en bijbehorende verwachtingen noemen we de afdekking van die verplichting.-}

dektAf :: Afdekking -> Verplichting PRAGMA "" " wordt afgedekt volgens de specificaties van "
PURPOSE RELATION dektAf IN DUTCH
{+Elke verplichting moet kunnen worden 'afgedekt' door een verzameling verwachtingen die deze afdekking specificeren. Een verzmaleing van verwachtingen die is bedoeld om een specifieke verplichting af te dekken, noemen we een afdekking van die verplichting.-}

specificatie :: Afdekking * Verwachting PRAGMA "Een van de specificaties waaraan moet zijn voldaan om aan de verplichting van " " te voldoen, is " 
PURPOSE RELATION specificatie IN DUTCH
{+Het moet mogelijk zijn om, voor de afdekking van een verplichting, verwachtingen te specificeren.-}

--RULE afdekkingID: I[Afdekking] = dektAf; dektAf~ /\ (specificatie <> specificatie~)
-- r <> s = (r ! -s) /\ (-r ! s)
RULE afdekkingID: I[Afdekking] = dektAf; dektAf~ /\ (specificatie ! -specificatie~) /\ (-specificatie ! specificatie~) 
PHRASE "Een afdekking wordt gekarakteriseerd door zijn verplichting en de verzameling van alle bijbehorende verwachtingen."
PURPOSE RULE afdekkingID IN DUTCH
{+Om vast te kunnen stellen dat een verplichting compleet wordt afgedekt door een verzameling verwachtingen, is het nodig om over een afdekking te kunnen spreken. Het spreekt welhaast vanzelf dat deze uniek wordt bepaald door deze verplichting en alle bijbehorende verwachtingen.-}

RULE "functiespecificatie": verwachtingVan = specificatie~; dektAf; verplichtingVan
PHRASE "Elke verwachting hoort bij een afdekking en is een verwachting van de asset die verantwoordelijk is voor het waarmaken van de verplichting van die afdekking."
PURPOSE RULE "functiespecificatie" IN DUTCH
{+Om te waarborgen dat de verantwoordelijkheid voor het waar maken van een verplichting op dezelfde plek is belegd als de verantwoordelijkheid voor de specificaties voor de inrichting van het bijbehorende werk, eisen we dat de asset (eigenaar) alle verwachtingen specificeert die nodig zijn om zijn verplichtingen mee af te dekken. Om te voorkomen dat er teveel werk wordt gespecificeerd, eisen we ook dat elke verwachting deel uitmaakt van (tenminste) een afdekking, zodat traceerbaar is aan welke requirement(s) van de asset de verwachting bijdraagt.-}

isVolledig :: Afdekking * Afdekking [PROP] PRAGMA "Er is besloten dat de verzameling van verwachtingen horende bij " " de verplichting van " " compleet (en dus op een acceptabele wijze) afdekken"
PURPOSE RELATION isVolledig IN DUTCH
{+Van elke afdekking moet kunnen worden vastgesteld dat deze volledig is, namelijk als kan worden geconcludeerd dat aan de verplichting is voldaan als (in voldoende mate) aan elke verwachting van die afdekking is voldaan. Van elke afdekking waarvoor dit is vastgesteld zeggen we dat deze de eigenschap 'isVolledig' heeft.-}

isGekozen :: Afdekking * Afdekking [PROP] PRAGMA "Er is besloten dat voor de verplichting horende bij " ", de keuze (uit alle mogelijke afdekkingen) om die verplichting af te dekken is gevallen op "
PURPOSE RELATION isGekozen IN DUTCH
{+Om aan een verplichting te doen zijn meerdere mogelijkheden. Elk daarvan wordt gespecificeerd door een afdekking. Van al deze mogelijkheden moet kunnen worden vastgesteld welke de voorkeur geniet om operationeel te worden gebruikt. Van de afdekking die hiervoor gekozen wordt, zeggen we dat deze de eigenschap 'isGekozen' heeft.-}

RULE "maximaal 1 afdekking per verplichting kiezen": isGekozen; dektAf; dektAf~; isGekozen |- I[Afdekking]
PHRASE "Voor elke verplichting is hoogstens een afdekking die de eigenschap 'isGekozen' heeft"
PURPOSE RULE "maximaal 1 afdekking per verplichting kiezen" IN DUTCH
{+Als voor de operationele afdekking van een verplichting is gekozen, moet deze wel eenduidig zijn; dit wil zeggen dat per verplichting maximaal 1 afdekking geoperationaliseerd mag worden.-}

ENDPATTERN
--[Risico Management]---------------------------------------
PATTERN "Risico Management" 
PURPOSE PATTERN "Risico Management" IN DUTCH
{+Om de inrichting van een asset zodanig te kunnen specificeren dat deze volgens zijn verplichtingen/specificaties functioneert en blijft functioneren, richten we ‘risico management’ (RM) in. RM waarborgt dat de specificatie van de inrichting van de asset zich kenmerkt doordat de risico’s op het niet (kunnen) nakomen van de verplichtingen van die asset acceptabel zijn. Daarbij worden die risico’s ingeschat gegeven de gespecificeerde inrichting van de asset.-}

RULE "acceptatie van asset risicos": acceptabelRestrisico = dRestRisico \/ ((I /\ verplichtingVan~;verplichtingVan) /\ (-verplichtingVan~ !(dektAf~; isGekozen; risicoGeaccepteerd; dektAf; verplichtingVan)))
PHRASE "Alle risico's van het niet nakomen van verplichtingen van een asset zijn in hun gezamenlijkheid ofwel expliciet geaccepteerd middels een besluit, ofwel impliciet geaccepteerd doordat van elke individuele verplichting het risico (gegeven dat daarvoor een afdekking is met de eigenschap 'isGekozen') is geaccepteerd."
PURPOSE RULE "acceptatie van asset risicos" IN DUTCH
{+Er zijn verschillende mogelijkheden om vast te stellen dat het restrisico van een asset acceptabel is (voor diens eigenaar). Dat kan bijvoorbeeld door expliciet daartoe te besluiten. Het kan ook impliciet, als voor elke verplichting is vastgesteld dat het bijbehorende risico acceptabel is. Omdat dit risico afhangt van de gekozen afdekking, moet ook eenduidig zijn vastgesteld om welke afdekking het gaat. Ook moet deze afdekking de eigenschap 'isGekozen' hebben, omdat het restrisico van de asset, als die eenmaal operationeel is, van de afdekkingen afhangt die deze eigenschap hebben.
Voor de volledigheid zeggen we dat het acceptabel zijn van het restrisico niet kan worden vastgesteld voor assets waaraan geen verplichting is gekoppeld.-}

RULE "restrisico acceptatie": dRestRisico |- -verplichtingVan~ ! (dektAf~; isGekozen; V)
PHRASE "Als is besloten het restrisico van een asset te accepteren, dan is van alle verplichtingen van die asset besloten hoe ze worden afgedekt."
PURPOSE RULE "restrisico acceptatie" IN DUTCH
{+Het besluit om het restrisico van een asset te accepteren kan alleen worden genomen als van alle verplichtingen bekend is hoe ze worden afgedekt-}

dRestRisico :: Asset * Asset [PROP] PRAGMA "De eigenaar van " " heeft besloten dat het restrisico van " " acceptabel is"
PURPOSE RELATION dRestRisico IN DUTCH
{+De eigenaar van een asset moet kunnen besluiten dat de risico's van alle verplichtingen van die asset in hun gezamenlijkheid acceptabel zijn, ook als dit niet voor elke individuele verplichting geldt.-}

risicoGeaccepteerd :: Afdekking * Afdekking [PROP] PRAGMA "Het risico dat de verplichting horende bij  " " niet wordt nagekomen is acceptabel".
PURPOSE RELATION risicoGeaccepteerd IN DUTCH
{+Van elke afdekking (van een verplichting) moet kunnen worden vastgesteld of het bijbehorende c.q. resterende risico acceptabel is. Als dit zo is, zeggen we dat de afdekking de eigenschap 'risicoGeaccepteerd' heeft. We kunnen dan ook zeggen dat het risico van de verplichting (onder deze afdekking) is geaccepteerd.-}

RULE "acceptatie van individuele risicos": risicoGeaccepteerd = acceptabelRisico \/ (I /\ (risicoInschatting \/ dektAf; impactInschatting \/ (-specificatie ! kansInschatting)); (I \/ lth); voldoendeLaag; V)
PHRASE "Een afdekking van een verplichting heeft de eigenschap 'risicoGeaccepteerd' dan en slechts dan als er ofwel een besluit ligt die dat risico uitdrukkelijk accepteert ('acceptabelRisico'), ofwel dat de inschatting van (a) ofwel het risico, (b) ofwel de impact, (c) ofwel de kans op het niet uitkomen van elke verwachting, als (voldoende) laag is ingeschat."
PURPOSE RULE "acceptatie van individuele risicos" IN DUTCH
{+Van elke afdekking moet kunnen worden vastgesteld of het risico op het niet waar kunnen maken van diens verplichting, al dan niet acceptabel is.-}

ENDPATTERN
--[Risico Analyse]------------------------------------------
PATTERN "Risico Analyse"
PURPOSE PATTERN "Risico Analyse" IN DUTCH
{+Om risico's goed in te kunnen schatten moet worden geanalyseerd waar ze mee te maken hebben. Enerzijds is dat de te verwachten (maximale) schade die kan ontstaan uit het niet nakomen van verplichtingen. Anderzijds is ook van belang in welke mate erop kan worden vertrouwd dat de verwachtingen die een verplichting afdekken, ook daadwerkelijk zullen worden waargemaakt. Uiteindelijk blijven risico's subjectieve inschattingen, ook als die op een analyse van dreigingen en schades berusten.-}

risicoInschatting :: Afdekking * InschattingsWaarde [UNI] PRAGMA "De hoogte van het risico (op het niet (kunnen) nakomen van de verplichting) horende bij " " is ingeschat als "
PURPOSE RELATION risicoInschatting IN DUTCH
{+Voor elke verplichting van een asset moet een inschatting kunnen worden gemaakt van het risico dat niet aan die verplichting zal/kan worden voldaan."-}

--!Het onderstaande moet nog worden bijgesteld, namelijk met behulp van de gekozen afdekking
isAfhankelijkVan :: Verplichting * Verwachting PRAGMA "Om aan " " te kunnen voldoen is het nodig dat aan " " wordt voldaan"
PURPOSE RELATION isAfhankelijkVan IN DUTCH
{+Om een verplichting (van een asset) na te (kunnen) komen, moet werk worden verzet. Het werk dat nodig is om aan een verplichting te kunnen voldoen, wordt door een verzameling van verwachtingen gespecificeerd.-}

RULE afhankelijkheden: isAfhankelijkVan = dektAf~; specificatie
PHRASE "Een verplichting is afhankelijk van elke verwachting die tot diens afdekking behoort. Omgekeerd geldt ook dat als een verwachting tot de afdekking van een verplichting hoort, de verplichting daarvan afhankelijk is."
PURPOSE RULE afhankelijkheden IN DUTCH
{+Om de discussies over risico's niet te hoeven verwarren met discussies over de afdekking van verplicthingen, willen we kunnen spreken over de afhankelijkheid die een verplicthing heeft van de bijbehorende verwachtingen.-}

kansInschatting :: Verwachting * InschattingsWaarde [UNI] PRAGMA "De hoogte van kans dat niet aan " " zal worden voldaan is ingeschat als "
PURPOSE RELATION kansInschatting IN DUTCH
{+Voor elke verwachting moet een inschatting kunnen worden gemaakt van de kans dat niet aan die verwachting zal worden voldaan."-}

impactInschatting :: Verplichting * InschattingsWaarde [UNI] PRAGMA "De maximale schade die de asset eigenaar leidt door het niet nakomen van " " is ingeschat als "
PURPOSE RELATION risicoInschatting IN DUTCH
{+Voor elke verplichting van een asset moet een inschatting kunnen worden gemaakt van de maximale schade die de asset eigenaar leidt als niet aan de verplichting zal/kan worden voldaan."-}

acceptabelRisico :: Afdekking * Afdekking [PROP] PRAGMA "Van het risico op het niet (kunnen) nakomen van de verplichting die door " " wordt afgedekt, is besloten dat het acceptabel is".
PURPOSE RELATION acceptabelRisico IN DUTCH
{+Als besloten wordt met welke afdekking een verplichting wordt afgedekt, moet daarvan ook besloten kunnen worden dat het bijbehorende risico (op het niet (kunnen) nakomen van die verplichting - gegeven deze afdekking), acceptabel is. Als het laatstbedoelde besluit is genomen, dan zeggen we dat de afdekking de eigenschap 'acceptabelRisico' heeft.-}

ENDPATTERN
--[InschattingsWaardes]----------------------------------------------
PATTERN "InschattingsWaardes"
PURPOSE PATTERN InschattingsWaardes IN DUTCH
{+Om vast te kunnen stellen of inschattingen risico's, kansen e.d. voldoende laag zijn, dan wel deze met elkaar te kunnen vergelijken, moet dit (ook) van InschattingsWaardes kunnen worden vastgesteld.-}

CONCEPT InschattingsWaarde "een element uit een verzameling (bijvoorbeeld {'L', 'M', 'H'}), die de hoogte van een inschatting (Laag, Midden of Hoog) vertegenwoordigt van bijvoorbeeld (maar niet uitsluitend) een risico"

RULE "inschattingswaarde verzameling": I[InschattingsWaarde] = 'L' \/ 'M' \/ 'H' 
PHRASE "Naast L(aag), M(idden), en H(oog) zijn geen andere InschattingsWaarde-scores mogelijk."
PURPOSE RULE "inschattingswaarde verzameling" IN DUTCH
{+De verzameling van waarden waarmee inschattingen van bijvoorbeeld risico's kunnen worden gescoord, moeten zijn vastgelegd.-}

voldoendeLaag :: InschattingsWaarde * InschattingsWaarde [PROP] PRAGMA "Alle inschattingswaardes die kleiner zijn dan " " of gelijk zijn aan " ", gelden als 'voldoende laag'"  = [ ("L", "L")].

lth :: InschattingsWaarde * InschattingsWaarde [TRN,ASY] PRAGMA "" " is kleiner/lager dan " = [ ("L", "M"); ("L", "H"); ("M", "H") ].
PURPOSE RELATION lth[InschattingsWaarde*InschattingsWaarde] IN DUTCH
{+Het moet mogelijk zijn om van twee inschattingen (van bijvoorbeeld risico's) vast te stellen welke daarvan de grootste c.q. kleinste is, dan wel of ze gelijkwaardig zijn.-}


ENDPATTERN