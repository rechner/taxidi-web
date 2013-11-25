        CREATE TABLE data(id SERIAL primary key,
name text, lastname text, dob text, activity integer,
room integer, grade text, phone text,
"mobileCarrier" integer, paging text, parent1 text,
parent2 text, "parent1Link" text, "parent2Link" text,
"parentEmail" text, medical text, "joinDate" DATE,
"lastSeen" DATE, "lastModified" TIMESTAMP, count integer,
visitor bool, expiry text, "noParentTag" bool,
barcode integer, picture text, authorized integer,
unauthorized integer, notes text);

        CREATE TABLE barcode(id SERIAL primary key ,
value text NOT NULL, ref integer REFERENCES "data"(id));

        CREATE TABLE authorized(id SERIAL,
ref integer, name text, lastname text, dob text,
"docNumber" text, photo text, document text, "phoneHome" text,
"phoneMobile" text, "mobileCarrier" integer, notes text);

        CREATE TABLE unauthorized(id SERIAL,
ref integer, name text, lastname text, photo text,
document text, phone text, notes text);

        CREATE TABLE volunteers(id SERIAL,
name text, lastname text, dob text, email text,
username text, "phoneHome" text, "phoneMobile" text,
"mobileCarrier" integer, "backgroundCheck" bool,
"backgroundDocuments" text, profession text, title text,
company text, "jobContact" text, address text, city text,
zip text, state text, country text, nametag bool,
category text, subtitle text, services text, rooms text,
"notifoUser" text, "notifoSecret" text,
availability text, "joinDate" DATE, "lastSeen" DATE,
"lastModified" TIMESTAMP, picture text, notes text);

        CREATE TABLE categories(id SERIAL,
name text, admin integer);

        CREATE TABLE users(id SERIAL,
"user" text UNIQUE NOT NULL, hash text, salt text,
admin bool, "notifoUser" text, "notifoSecret" text,
"scATR" text, "leftHanded" bool, ref int, name text);

        CREATE TABLE activities(id SERIAL,
name text, prefix text, "securityTag" text, "securityMode" text,
"nametagEnable" bool, nametag text,
"parentTagEnable" bool, "parentTag" text,
admin integer, "autoExpire" bool, "notifyExpire" bool,
newsletter bool, "newsletterLink" text,
"registerSMSEnable" bool, "registerSMS" text,
"registerEmailEnable" bool, "registerEmail" text,
"checkinSMSEnable" bool, "checkinSMS" text,
"checkinEmailEnable" bool, "checkinEmail" text,
"parentURI" text, "alertText" text);

        CREATE TABLE services(id SERIAL,
name text, day integer, time TIME, "endTime" TIME);

        CREATE TABLE rooms(id SERIAL,
name text NOT NULL, activity integer NOT NULL,
"volunteerMinimum" integer, "maximumOccupancy" integer, camera text,
"cameraFPS" integer, admin integer, "notifoUser" text, "notifoSecret" text,
email text, mobile text, carrier integer);

        self.tableSQL.append( """CREATE TABLE carriers(id SERIAL,
name text, region text, address text, subject text,
message text);

        CREATE TABLE statistics(id SERIAL,
person integer, date date,
service text, expires text,
checkin timestamp, checkout timestamp, code text, location text,
volunteer integer, activity text, room text);

--	password is "demopass"
	 INSERT INTO users ("user", hash, salt, admin, name) VALUES
('admin', '18f628fa7ab8cdba86d1e8d711b7773d268be83bd774efbc03927f2aae8a354d',
'lamesalt', 't', 'Admin')
	
