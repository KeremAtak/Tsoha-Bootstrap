### Tietokohde: Alcoholic
Attribuutti | Arvojoukko | Kuvaus
--------------- | ----- | ------
username|Merkkijono, 1-30 merkkiä, uniikki|Käyttäjän käyttäjänimi
password|Merkkijono, 1-30 merkkiä|Käyttäjän salasana

Alcoholic on palvelun käyttäjä. Vain Alcoholicilla on oikeus käyttää palvelua.

### Tietokohde: Ingredient
Attribuutti | Arvojoukko | Kuvaus
--------------- | ----- | ------
name|Merkkijono, 1-50 merkkiä|Ainesosan nimi
alcohol_percentage|Kokonaisluku|Ainesosan alkoholiprosentti
description|Merkkijono, 0-500 merkkiä|Ainesosan kuvaus

Ingredient on ainesosa joilla käyttäjä luo drinkin.


### Tietokohde: Drink
Attribuutti | Arvojoukko | Kuvaus
--------------- | ----- | ------
name|Merkkijono, 1-50 merkkiä|Drinkin nimi nimi
volume|Kokonaisluku|Drinkin tilavuus millimetreinä.
alcohol_percentage|Kokonaisluku|Ainesosan alkoholiprosentti
description|Merkkijono, 0-500 merkkiä|Drinkin kuvaus

Drinkki on palvelun keskeinen taulu. Käyttäjät lukevat reseptejä ja arvioivat niitä.


### Tietokohde: Review
Attribuutti | Arvojoukko | Kuvaus
--------------- | ----- | ------
rating|Murtoluku, 0-5|Drinkin numeraalinen arvio 
description|Merkkijono, 0-500 merkkiä| kuvaus

Rating on drinkin arvio joita käyttäjät luovat.
