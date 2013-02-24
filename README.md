# Deliberativní systém hodnocení projektů

Funkční instance je možné vidět na stipendia.servismag.cz.

# Roadmap

- 1.0: funkční systém pro potřeby Stipendijní komise FSS
     - *hotovo* práce s kritérii hodnocení, dokončení vytváření kol hodnocení
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

# Instalace:

Zatím jsem neřešil vytváření dalších instancí, ale pokud byste si chtěli s aplikací pohrát, stačí vytvořit databázi ze souboru /db.sql a nastavit přístup k databázi v souboru app/config.ini (v repoztáři najdete config.blank.ini, který stačí přejmenovat a doplnit).
