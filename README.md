# Deliberativní systém hodnocení projektů
<<<<<<< HEAD:README

Projekt ještě prochází turbulentním vývojem, jeho aktuální funkční instanci můžete vidět na stipendia.servismag.cz.
=======
>>>>>>> Update readme:README.md

Funkční instance je možné vidět na stipendia.servismag.cz.

<<<<<<< HEAD:README
# Roadmap

1.0: funkční systém pro potřeby Stipendijní komise FSS
     - práce s kritérii hodnocení, dokončení vytváření kol hodnocení
=======

# Roadmap

- 1.0: funkční systém pro potřeby Stipendijní komise FSS
     - *hotovo* práce s kritérii hodnocení, dokončení vytváření kol hodnocení
>>>>>>> Update readme:README.md
     - integrovaný manuál / first steps
     - odladění používaných termínů
     - *hotovo* integrace pravidel
     - práce s porotci
     - *hotovo* zobrazení konečného hodnocení
     - doplnění logování důležitých věcí

- 2.0: nastavení a modularizace prvků, které variují
     - update na PHP 5.3
     - speciální třída, kde je možné dodefinovat některé podstatné detaily
     - způsob ukládání žádostí
     - počet hodnotitelů
     - generování odkazů na žadatele (identifikátory lidí)

- 3.0: internacionalizace
     - překlad do AJ
     - skloňování, kde je potřeba
     - přepis informací o projektu do angličtiny


# Závislosti:

– Nette 2.0 dev pro PHP 5.2
– dibi
– texy 2

Knihovny nahrávejte do složky /libs/.

<<<<<<< HEAD:README
## Instalace:
_____________
Zatím jsem neřešil vytváření dalších instancí, ale pokud byste si chtěli s aplikací pohrát, stačí vytvořit databázi ze souboru /db.sql a nastavit přístup k databázi v souboru app/config.ini (v repoztáři najdete config.blank.ini, který stačí přejmenovat a doplnit).
=======
# Instalace:

Zatím jsem neřešil vytváření dalších instancí, ale pokud byste si chtěli s aplikací pohrát, stačí vytvořit databázi ze souboru /db.sql a nastavit přístup k databázi v souboru app/config.ini (v repoztáři najdete config.blank.ini, který stačí přejmenovat a doplnit).

Při vytváření hodnotícího kola je pak nutné ručně v databázi vytvořit hodnotící kritéria (tabulka ratingCategories) a přiřadit je k danému kolu hodnocení (tabulka roundXratingCategory). Ostatní funkčnost pak již frčí přes UI.
>>>>>>> Update readme:README.md
