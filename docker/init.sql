CREATE TABLE profile (
    slug character varying(255) NOT null,
    name character varying(255) NULL,
    CONSTRAINT pk_slug PRIMARY KEY (slug)
);

INSERT INTO profile (slug, name) VALUES ('entrepreneur', 'Entrepreneur'), ('investor','Investor');


CREATE TABLE user_type (
    name character varying(255) NOT null,
    CONSTRAINT pk_name PRIMARY KEY (name)
);

INSERT INTO user_type (name) VALUES ('physical'), ('legal');


CREATE TABLE identification_type (
    slug character varying(255) NOT null,
    name character varying(255) NULL,
    CONSTRAINT pk_identification_type_slug PRIMARY KEY (slug)
);

INSERT INTO identification_type (slug, name) VALUES ('dni', 'DNI'), ('nie','NIE'),('nif', 'NIF'),('passport', 'Passport');


CREATE TABLE nationality (
    name character varying(255) NOT null,
    CONSTRAINT pk_name_nationality PRIMARY KEY (name)
);

INSERT INTO nationality (name) VALUES ('española'),('afgana'),('albanesa'),('alemana'),('andorrana'),('angoleña'),('antiguana'),('saudí'),('argelina'),('argentina'),('armenia'),('arubeña'),('australiana'),('austriaca'),('azerbaiyana'),('bahameña'),('bangladesí'),('barbadense'),('bareiní'),('belga'),('beliceña'),('beninésa'),('bielorrusa'),('birmana'),('boliviana'),('bosnia'),('botsuana'),('brasileña'),('bruneana'),('búlgara'),('burkinés'),('burundésa'),('butanésa'),('caboverdiana'),('camboyana'),('camerunesa'),('canadiense'),('catarí'),('chadiana'),('chilena'),('china'),('chipriota'),('vaticana'),('colombiana'),('comorense'),('norcoreana'),('surcoreana'),('marfileña'),('costarricense'),('croata'),('cubana'),('danésa'),('dominiqués'),('ecuatoriana'),('egipcia'),('salvadoreña'),('emiratí'),('eritrea'),('eslovaca'),('eslovena'),('estadounidense'),('estonia'),('etíope'),('filipina'),('finlandésa'),('fiyiana'),('francésa'),('gabonésa'),('gambiana'),('georgiana'),('gibraltareña'),('ghanésa'),('granadina'),('griega'),('groenlandésa'),('guatemalteca'),('ecuatoguineana'),('guineana'),('guyanesa'),('haitiana'),('hondureña'),('húngara'),('hindú'),('indonesia'),('iraquí'),('iraní'),('irlandésa'),('islandésa'),('cookiana'),('marshalésa'),('salomonense'),('israelí'),('italiana'),('jamaiquina'),('japonésa'),('jordana'),('kazaja'),('keniata'),('kirguisa'),('kiribatiana'),('kuwaití'),('laosiana'),('lesotense'),('letóna'),('libanésa'),('liberiana'),('libia'),('liechtensteiniana'),('lituana'),('luxemburguésa'),('malgache'),('malasia'),('malauí'),('maldiva'),('maliense'),('maltésa'),('marroquí'),('martiniqués'),('mauriciana'),('mauritana'),('mexicana'),('micronesia'),('moldava'),('monegasca'),('mongola'),('montenegrina'),('mozambiqueña'),('namibia'),('nauruana'),('nepalí'),('nicaragüense'),('nigerina'),('nigeriana'),('noruega'),('neozelandésa'),('omaní'),('neerlandésa'),('pakistaní'),('palauana'),('palestina'),('panameña'),('papú'),('paraguaya'),('peruana'),('polaca'),('portuguésa'),('puertorriqueña'),('británica'),('centroafricana'),('checa'),('macedonia'),('congoleña'),('dominicana'),('sudafricana'),('ruandésa'),('rumana'),('rusa'),('samoana'),('cristobaleña'),('sanmarinense'),('sanvicentina'),('santalucense'),('santotomense'),('senegalésa'),('serbia'),('seychellense'),('sierraleonésa'),('singapurense'),('siria'),('somalí'),('ceilanésa'),('suazi'),('sursudanésa'),('sudanésa'),('sueca'),('suiza'),('surinamesa'),('tailandésa'),('tanzana'),('tayika'),('timorense'),('togolésa'),('tongana'),('trinitense'),('tunecina'),('turcomana'),('turca'),('tuvaluana'),('ucraniana'),('ugandésa'),('uruguaya'),('uzbeka'),('vanuatuense'),('venezolana'),('vietnamita'),('yemení'),('yibutiana'),('zambiana'),('zimbabuense'),('otra');


CREATE TABLE users
(
    uuid uuid NOT NULL,
    first_name character varying(255) DEFAULT NULL,
    last_name character varying(255) DEFAULT NULL,
    user_type character varying(255) DEFAULT NULL,
    profile character varying(255) DEFAULT NULL,
    email character varying(255) DEFAULT NULL,
    password character varying(64) DEFAULT NULL,
    token character varying(64) DEFAULT NULL,
    create_at integer DEFAULT 0,
    update_at integer DEFAULT 0,
    delete_at integer DEFAULT 0,
    sended_mail_at integer DEFAULT 0,
    address character varying(255) DEFAULT NULL,
    telephone character varying(255) DEFAULT NULL,
    nationality character varying(255) DEFAULT NULL,
    identification_type character varying(255) DEFAULT NULL,
    identification_value character varying(255) DEFAULT NULL,
    marital_status character varying(255) DEFAULT NULL,
    matrimonial_regime character varying(255) DEFAULT NULL,
    profession character varying(255) DEFAULT NULL,
    CONSTRAINT pk_user_uuid PRIMARY KEY (uuid),
    CONSTRAINT fk_user_user_type FOREIGN KEY (user_type)
        REFERENCES user_type (name) MATCH SIMPLE
        ON UPDATE CASCADE,
    CONSTRAINT fk_user_nationality FOREIGN KEY (nationality)
        REFERENCES nationality (name) MATCH SIMPLE
        ON UPDATE CASCADE,
    CONSTRAINT fk_user_profile FOREIGN KEY (profile)
        REFERENCES profile (slug) MATCH SIMPLE
        ON UPDATE CASCADE,
    CONSTRAINT fk_user_identification_type FOREIGN KEY (identification_type)
        REFERENCES identification_type (slug) MATCH SIMPLE
        ON UPDATE CASCADE,
    UNIQUE(email)
);

CREATE TABLE company
(
    uuid uuid NOT NULL,
    user_uuid uuid,
    name character varying(255) DEFAULT NULL,
    registry_data json DEFAULT NULL,
    administration_organ character varying(255) DEFAULT NULL,
    logo character varying(255) DEFAULT NULL,
    comercial_name character varying(255) DEFAULT NULL,
    web character varying(255) DEFAULT NULL,
    legal_representative json DEFAULT NULL,
    contact_person json DEFAULT NULL,
    employees_number integer DEFAULT 0,
    documentation character varying(255) DEFAULT NULL,
    society_statutes character varying(255) DEFAULT NULL,
    partners_agreement character varying(255) DEFAULT NULL,
    business_plan text,
    brand_detail text,
    investor_portfolio json DEFAULT NULL,
    create_at integer DEFAULT 0,
    update_at integer DEFAULT 0,
    delete_at integer DEFAULT 0,
    sector character varying(255) DEFAULT NULL,
    beneficial_ownership json DEFAULT NULL,
    CONSTRAINT pk_company_uuid PRIMARY KEY (uuid),
    CONSTRAINT fk_company_user FOREIGN KEY (user_uuid)
        REFERENCES users(uuid) ON DELETE CASCADE
);


CREATE TABLE participation (
    uuid uuid NOT NULL,
    user_uuid uuid,
    participation_id character varying(255),
    purchase_date integer DEFAULT 0,
    sale_date integer DEFAULT 0,
    percentage integer DEFAULT 0,
    CONSTRAINT pk_participation_uuid PRIMARY KEY (uuid),
    CONSTRAINT fk_participation_user FOREIGN KEY (user_uuid)
        REFERENCES users (uuid)
);


CREATE TABLE investment (
    uuid uuid NOT NULL,
    user_uuid uuid,
    company_name character varying(255),
    logo character varying(255),
    brand_detail text,
    current_situation character varying(255),
    partners_agreement boolean DEFAULT false,
    business_plan text,
    lead_investor boolean DEFAULT false,
    investor_type character varying(255),
    documentation character varying(255),
    num_participation integer DEFAULT 0,
    percentage_participation DOUBLE PRECISION,
    social_capital DOUBLE PRECISION,
    last_post_money_valoration integer DEFAULT 0,
    annex_type character varying(32),
    CONSTRAINT pk_investment_uuid PRIMARY KEY (uuid),
    CONSTRAINT fk_investment_user FOREIGN KEY (user_uuid)
        REFERENCES users (uuid)
);
