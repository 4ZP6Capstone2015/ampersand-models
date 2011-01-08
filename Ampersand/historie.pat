PATTERN Historie -- MAINTAINER stef.joosten@ou.nl 
-- modified by rieks.joosten@tno.nl)
PURPOSE PATTERN Historie
{+Een historische database wordt ingezet zodat gebruikers kunnen nagaan wat geregistreerd is geweest op enig moment in het verleden.
Een vraag als: ``Waar woonde Pieter de Vries op 12 oktober 1959'' is standaard uitvraagbaar in een historische woonaddressenregistratie.
Daartoe maken we onderscheid tussen een object en de inhoud van dat object.
De inhoud kan immers wijzigen in de loop van de tijd.
Uitgangspunt is dat de inhoud van een object wijzigt op basis van een gebeurtenis.
Dit pattern specificeert een administratie van gebeurtenissen die het mogelijk maakt de inhoud van objecten in het verleden te reconstrueren.
-}
CONCEPT Inhoud "De inhoud van een object kan van alles zijn." ""
CONCEPT Versie "Een versienummer representeert de relatieve leeftijd van een inhoud  ten opzichte van eerdere inhouden" ""
CONCEPT Gebeurtenis "Elke aanleiding om de inhoud van een object te creeren, lezen, veranderen of verwijderen noemen we een gebeurtenis. Het is uitdrukkelijk toegelaten dat een enkele gebeurtenis de inhoud van meerdere objecten bewerkt, bijvoorbeeld het ene aanmaakt, vier andere leest, een volgende wijzigt en er een aantal verwijdert." ""

KEY keyInhoud: Inhoud(object,versie)
--RULE keyInhoud MAINTAINS object;object~ /\ versie;versie~ |- I
--  EXPLANATION "De inhoud van een object is uniek gekarakteriseerd door het versienummer. Dat wil zeggen: als het object en het versienummer vastliggen, dan is er één inhoud (of er is geen inhoud)."
--  EXPLANATION "When the object and versie number is known, any object is uniquely determined. Note the difference with 'object', which identifies the most recent versie of an object."

object :: Inhoud -> Object                        PRAGMA "" "hoort bij" "".
PURPOSE RELATION object 
{+Als we zeggen dat een inhoud bij een object hoort,
dan bedoelen we dat dat object ooit deze inhoud heeft gehad.
Oudere versies van die inhoud blijven bewaard,
zodat de koppeling naar het object ook bewaard moet blijven.
-}
versie     :: Inhoud -> Versie                    PRAGMA "Het versienummer van" "is".
PURPOSE RELATION versie 
{+Om de tijdsvolgorde te registreren krijgt elke inhoud een versienummer.
-}
--RJ:**Ik zie liever eerst de relaties en dan de regels die er gebruik van maken zodat de ADL leesbaarder wordt voor reviewers. Dat je dat in de funcspec omkeert is ook prima, maar die lees je dan ook anders.**
RULE "actuele inhoud" MAINTAINS versie~;object;inhoud;versie |- lt \/I[Versie]
PURPOSE RULE "actuele inhoud" IN DUTCH
{+Op elk moment moet een object verwijzen naar zijn actuele inhoud.
Daarom verwijst de relatie 'inhoud' naar de meest recente inhoud van een object.
-}
PURPOSE RULE "actuele inhoud" IN ENGLISH
{+On any given moment in time, an object must refer to its most recent content.
That is why the relation 'inhoud' (content) points to the most recent content of an object."
-}

lt :: Versie * Versie {-[ASY,TRN]-} PRAGMA "" "is ouder dan".
PURPOSE RELATION lt
{+Versies hebben zin zolang we kunnen vaststellen voor elk tweetal versies welk van de twee ouder is.
Daarom bestaat een relatie "lt", die aangeeft of een versie ouder is dan een andere.
-}
inhoud      :: Object -> Inhoud                    PRAGMA "" "bevat op het actuele moment".
PURPOSE RELATION inhoud 
{+
Deze inhoud kan vervangen worden, waarbij het object een volgend versienummer krijgt.
We willen dan dat het object te allen tijde naar het jongste object verwijst.
-}

RULE "Opvolgend versienummer" MAINTAINS pre~;post /\ object;object~ /\ -I |- versie;volgtOp~;versie~
  EXPLANATION "Als door het optreden van een gebeurtenis de inhoud van een object is veranderd, dan is de versie van de nieuwe inhoud gelijk aan de opvolger van de versie heeft de oude inhoud."
PURPOSE RULE "Opvolgend versienummer" IN DUTCH
{+Van elke inhoud wordt een versie bijgehouden, om voor gebruikers de leeftijd ten opzichte van andere inhouden zichtbaar te maken. Als een inhoud verandert, krijgt die een opvolgende versie toegekend.
-}
PURPOSE RULE "Opvolgend versienummer" IN ENGLISH
{+A version number is maintained for the purpose of visualizing the age of a content relative to other contents. Whenever the content of an object changes, it will be assigned the consecutive version number.
-}
volgtOp :: Versie * Versie [ASY] PRAGMA "" "is de opvolger van, c.q. volgt direct op".
PURPOSE RELATION volgtOp IN DUTCH
{+Om vast te kunnen stellen dat van een een stuk object-geschiedenis geen enkele verandering ontbreekt, moeten we de opeenvolging van object inhouden kunnen natrekken. Dat doen we door expliciet de volgorde van versienummers vast te stellen.-}

RULE "volgtOp irreflexief" MAINTAINS volgtOp |- -I
RULE "kleiner dan" MAINTAINS (I \/ lt);volgtOp~ |- lt

--**RJ: ik zie geen reden om relaties 'pre' en 'post' een multipliciteit te geven - die waren overigens ook niet gemotiveerd. Daarom heb ik ze verwijderd.**
pre        :: Gebeurtenis*Inhoud PRAGMA "" "is de rechtstreekse aanleiding geweest om een operatie op" "uit te voeren".
PURPOSE RELATION pre IN DUTCH
{+Gebeurtenissen kunnen aanleiding geven de inhoud van een object veranderen, maar dat hoeft natuurlijk niet. Gebeurtenissen kunnen immers ook aanleiding zijn om de inhoud alleen maar te bekijken/inspecteren.
De inhoud pre(g) is input voor de operatie op(g)
-}
PURPOSE RELATION pre IN ENGLISH
{+When an event occurs, it may change an object.
The contents pre(g) is input for the operation op(g)
-}
--**RJ: ik zie geen reden om relaties 'pre' en 'post' een multipliciteit te geven - die waren overigens ook niet gemotiveerd. Daarom heb ik ze verwijderd.**
post       :: Gebeurtenis * Inhoud                     PRAGMA "De bewerking waartoe" "de directe aanleiding is geweest, heeft" "als resultaat opgeleverd".
PURPOSE RELATION post IN DUTCH
{+Gebeurtenissen kunnen de inhoud van een object veranderen, maar dat hoeft natuurlijk niet. Gebeurtenissen kunnen immers ook aanleiding zijn om de inhoud alleen maar te bekijken/inspecteren.
De inhoud post(g) is output van de operatie op(g)
-}
PURPOSE RELATION post IN ENGLISH
{+When an event occurs, it may change an object.
The contents post(g) is output of the operation op(g)
-}
op         :: Gebeurtenis -> Bewerking                  PRAGMA "" "heeft" "uitgevoerd".

RULE "changelog" MAINTAINS changed = (pre /\ post;-I);object /\ (post /\ pre;-I);object
PURPOSE RULE "changelog" IN DUTCH
{+In de 'changelog' kan van elke gebeurtenis die geleid heeft tot inhoudelijke veranderingen worden vastgesteld welke objecten dat betrof. Ook omgekeerd kan van elk object worden teruggevonden welke gebeurtenissen hebben geleid tot inhoudelijke veranderingen in het object.
-}

changed    :: Gebeurtenis * Object                      PRAGMA "De bewerking waartoe" "de directe aanleiding is geweest, heeft de inhoud van" "gewijzigd".
PURPOSE RELATION changed
{+When an event occurs, this results in a changed object. That object can be seen as an output to the operation that is being perfomed
-}

ENDPATTERN

PATTERN Feiten
PURPOSE PATTERN Feiten IN DUTCH
{+De BRP moet een koppeling onderhouden tussen feiten en bewijsmiddelen.
Zo onderbouwt een geboorteakte het feit dat iemand geboren is.
Door deze koppeling is elke uitspraak die de BRP doet traceerbaar tot de bron.
Wanneer een feit niet onderbouwd is, blijkt dat in de BRP,
bijvoorbeeld door afwezigheid van bewijsmiddelen of de aanwezigheid van juridisch zwakke bewijsmiddelen.
Wettelijk is de BRP verplicht om elk gegeven vanuit een deelnemer te toetsen alvorens het als waarheid aan te nemen.
Hierdoor is sprake van twee verschillende concepten, namelijk gegevens en feiten.
Gegevens worden aangeleverd door deelnemers, en feiten zijn die gegevens die de BPR naar afnemers presenteert.
Als "werkhypothese" kun je zeggen dat feiten die gegevens zijn, die door de BPR voor waar worden gehouden.
De BPR doet alleen uitspraken die op feiten gebaseerd zijn.
-}
GEN Feit ISA Gegeven
CONCEPT Feit "een gegeven, dat op enig moment door de BRP als waarheid is vastgesteld." "eigen"

  onderbouwt            :: Document * Feit          PRAGMA "" "is een bewijsmiddel voor".
PURPOSE RELATION onderbouwt IN DUTCH
{+Om feiten en bewijsmiddelen te koppelen, bestaat een relatie ``onderbouwt''.
-}
  betreft               :: Feit * Persoon       PRAGMA "" "gaat over".
PURPOSE RELATION betreft[Feit*Persoon] IN DUTCH
{+Om rechtmatig te kunnen omgaan met persoonlijke gegevens worden zij gekoppeld aan de betreffende persoon.
-}

-- Elk geconstateerd feit heeft één feittype, en wel de naam van dat feit.
  naam               :: Feit -> Feitnaam         PRAGMA "" "heeft".
PURPOSE RELATION naam[Feit*Feitnaam] IN DUTCH
{+
-}
  vastgesteldDoor    :: Feit -> Persoon          PRAGMA "" "is geconstateerd door".
PURPOSE RELATION vastgesteldDoor IN DUTCH
{+
-}
  vastgesteldOp      :: Feit -> Tijdstip         PRAGMA "" "is geconstateerd op".
PURPOSE RELATION vastgesteldOp IN DUTCH
{+
-}
  authentiek         :: Document * Authenticatie PRAGMA "" "is geauthenticeerd met". -- bijv. apostille, legalisatie, enz.
PURPOSE RELATION authentiek IN DUTCH
{+
-}
  persoonsdossier :: Document * Persoon   PRAGMA "" "zit in het persoonsdossier van".
PURPOSE RELATION persoonsdossier IN DUTCH
{+
-}
  RULE auth SIGNALS I /\ persoonsdossier;persoonsdossier~ |- authentiek;authentiek~
   EXPLANATION "documenten, die in het persoonsdossier zitten en nog moeten worden geauthenticeerd."
  onderbouwt;betreft |- persoonsdossier
   EXPLANATION "Alle onderbouwing van feiten betreffende een persoon moeten in diens persoonsdossier voorkomen."
ENDPATTERN

--Populatie van pattern; Feiten
POPULATION betreft[Feit*Persoon] CONTAINS
     [ ("110402", "Tanghasami")
     ; ("987237", "Tanghasami")
     ]
POPULATION naam[Feit*Feitnaam] CONTAINS
     [ ("110402", "huwelijk")
     ; ("987237", "paspoort gezien")
     ]
POPULATION vastgesteldDoor CONTAINS
     [ ("110402", "van der Knaap")
     ; ("987237", "van der Knaap")
     ]
POPULATION vastgesteldOp CONTAINS
     [ ("110402", "12 okt 1998")
     ; ("987237", "12 okt 1998")
     ]

PATTERN Multirealiteit
PURPOSE PATTERN Multirealiteit IN DUTCH
{+BRP erkent multirealiteit. Dit betekent dat strijdige feiten geregistreerd kunnen worden, bijvoorbeeld vanuit verschillende bronnen.
-}
PURPOSE RELATION bron IN DUTCH
{+Elke bewering wordt in de BRP geregistreerd met bron, waardoor de traceerbaarheid naar de bron gegarandeerd is.
-}
bron :: Gegeven -> Actor PRAGMA "" "is gedaan door".
ENDPATTERN

ROLE User EDITS object,versie
ROLE User USES inhoud, "Inhoud toevoegen", object

SERVICE inhoud(object,versie) : I[Inhoud]
 = [ inhoud    : I[Inhoud]
   , naam      : object
   , versie    : versie
   , opvolger  : pre~;post /\ versie;volgtOp~;versie~ /\ object;object~
   , voorganger : post~;pre /\ versie;volgtOp;versie~ /\ object;object~
   ]

SERVICE "Inhoud toevoegen" : I[Object]
 = [ object   : I[Object]
   , inhoud   : object~
       = [ inhoud    : I[Inhoud]
         , versie    : versie
         ]
   ]

SERVICE object : I[Object]
 = [ object   : I[Object]
   , inhoud   : object~ ; (-(pre~;post;post~;pre)/\I)
       = [ inhoud    : I[Inhoud]
         , versie    : versie
         ]
   , verloop  : object~
   ]

POPULATION volgtOp CONTAINS
   [ ("5","4")
   ; ("4","3")
   ; ("3","2")
   ; ("2","1")
   ]
POPULATION lt CONTAINS
   [ ("1","2")
   ; ("2","3")
   ; ("3","4")
   ; ("4","5")
   ; ("1","3")
   ; ("2","4")
   ; ("3","5")
   ; ("1","4")
   ; ("2","5")
   ; ("1","5")
   ]
POPULATION versie[Inhoud*Versie] CONTAINS
   [ ("leeg","1")
   ; ("beetje","2")
   ; ("meer","4")
   ; ("vol","5")
   ]
POPULATION object[Inhoud*Object] CONTAINS
   [ ("leeg","doc1")
   ; ("beetje","doc1")
   ; ("meer","doc1")
   ; ("vol","doc1")
   ]
POPULATION inhoud[Object*Inhoud] CONTAINS
   [ ("doc1","vol")
   ]
POPULATION bron[Gegeven*Actor] CONTAINS
   [ ("110402", "Tanghasami")
   ; ("987237", "Tanghasami")
   ]
