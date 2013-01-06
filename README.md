# Pok&eacute;mon Switching Statistics

This is a program that reads [Shoddy Battle][] battle logs in order to prepare
statistics regarding Pok&eacute;mon switching. This program was written by
[Cathy Fitzpatrick][cathyjf] in September 2009 and it is licensed under the
[GNU Affero General Public License][agpl3], version 3 or later.

## Definitions

In order to understand the statistics, it is necessary to define some terms.

Except for at the start of the battle, whenever one player sends out a new
pokemon X (including through Baton Pass and U-turn), if there is a pokemon Y on
the opponent's side of the field, then we say that pokemon X _switched into_
pokemon Y. We call pokemon X the **subject** of the switch in and pokemon Y the
**object** of the switch in.

We can sort the switch in statistics both _by subject_ and _by object_. Given a
particular pokemon X, the statistics sorted _by subject_ tell you the most
common pokemon that pokemon X **switches into**. Similarly, given a particular
pokemon X, the statistics sorted _by object_ tell you the most common pokemon
who **switch into** pokemon X.

### Interpretation example

Here is an example of how to interpret this data. This is an excerpt from the
statistics sorted by subject showing you the most common pokemon that Gyarados
switches into on the Standard ladder:

1. Scizor - 7.12% (12166)
2. Heatran - 6.37% (10897)
3. Swampert - 4.85% (8284)
4. Infernape - 4.62% (7907)
5. Salamence - 3.86% (6597)
6. Gyarados - 3.7% (6321)
7. Lucario - 3.34% (5715)

This means that in August 2009, Gyarados switched into Scizor 12166 times,
which was 7.12% of all switches in which Gyarados was the subject.

## August 2009 data

This program was run on the battle logs from Smogon's Shoddy Battle server for
August 2009. The results will be linked to here a bit later.

## Credits

+ [Cathy Fitzpatrick][cathyjf] created the program.
+ The program uses [HTML Tidy][], a binary for which is included in the
  repository.

[Shoddy Battle]: http://pokemonlab.com
[cathyjf]: https://cathyjf.com
[agpl3]: http://www.fsf.org/licensing/licenses/agpl-3.0.html
[HTML Tidy]: http://tidy.sourceforge.net/
