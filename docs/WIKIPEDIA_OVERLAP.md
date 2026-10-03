# Legacy posts compared with Wikipedia

Measured 4 October 2026 by `tmp/legacy-wiki-audit/audit.py`, read-only, from the live snapshot
`tmp/live-snapshot/posts-20261003-night.tsv.gz`. Each legacy post (ids up to 1140, Blog category left out)
was compared with the English Wikipedia page of the same title, with the same test that
`tmp/new-posts/check_new.py` applies to new posts. Per-post data: `tmp/legacy-wiki-audit/results.jsonl`.

## Result

- 1,092 posts compared. 1 has no Wikipedia page of that title.
- **837 fail the clone test (77%). 255 pass.**
- 821 posts contain at least one run of 12 or more words that is identical to Wikipedia; 17,863 such runs in all.
- 116 posts use Wikipedia's section headings (three or more, and at least half of ours).
- Share of our 6-word runs that are also in Wikipedia: median 14.5%, 75th percentile 35.7%, 90th 58.5%, highest 91.4%.
- For comparison, the 17 new posts written in October 2026 score 0.0% to 0.9%, with no identical runs.

| Shared wording | Posts |
|---|---|
| 50% or more | 163 |
| 25% to 50% | 216 |
| 10% to 25% | 248 |
| 4% to 10% | 149 |
| under 4% | 316 |

## What it means

The 6 September 2026 content release changed the stored content of 1,116 posts, put it in the five-block
format and added source lists. For most posts it did not replace Wikipedia's sentences. Post 889 (Bodo League
massacre) has paragraphs on the live site that are word for word Wikipedia's. The earlier audits checked
length, filler and Wikipedia links, not wording, so they did not see this.

Not compared: 26 posts in the Blog category (for example Sidhu Moose Wala and Nipsey Hussle).
Not yet known: which pipeline produced which post, and how many of the listed sources are real. Of three
links tested on post 889, one is dead and one could not be opened.

## Posts that fail, worst first

Fail = 4% or more shared wording, or an identical 12-word run, or Wikipedia's headings.

| Post | Title | Shared wording | Identical runs | Headings shared | Words |
|---|---|---|---|---|---|
| 889 | Bodo League massacre | 91.4% | 74 | 2 of 4 | 981 |
| 728 | Cherry Valley massacre | 89.0% | 92 | 3 of 5 | 1244 |
| 830 | Le Paradis massacre | 88.8% | 145 | 3 of 5 | 1971 |
| 886 | Batang Kali massacre | 88.3% | 90 | 2 of 3 | 1243 |
| 969 | Monrovia Church massacre | 88.2% | 51 | 4 of 4 | 720 |
| 811 | Coniston massacre | 87.9% | 188 | 2 of 6 | 2569 |
| 503 | Martha Rendell | 87.8% | 63 | 1 of 4 | 891 |
| 891 | No Gun Ri massacre | 86.4% | 129 | 2 of 5 | 1840 |
| 806 | Hanapepe massacre | 85.9% | 78 | 2 of 4 | 1108 |
| 981 | Bisho massacre | 83.2% | 80 | 5 of 5 | 1144 |
| 968 | Eastern University massacre | 83.2% | 67 | 4 of 5 | 1006 |
| 860 | Huta Pieniacka massacre | 82.9% | 58 | 2 of 3 | 877 |
| 977 | Khojaly massacre | 82.0% | 68 | 3 of 5 | 1019 |
| 509 | Marcus Einfeld | 81.8% | 96 | 2 of 7 | 1430 |
| 970 | 1990 Batticaloa massacre | 81.6% | 33 | 2 of 3 | 494 |
| 978 | Maraga massacre | 80.8% | 68 | 2 of 4 | 1049 |
| 424 | Ray O'Connor | 80.8% | 158 | 0 of 4 | 2424 |
| 971 | Aramoana massacre | 80.5% | 111 | 1 of 5 | 1671 |
| 743 | Kasos Massacre | 78.6% | 68 | 1 of 4 | 1035 |
| 270 | Sureños | 78.2% | 84 | 1 of 5 | 1327 |
| 975 | Vukovar massacre | 78.2% | 84 | 1 of 5 | 1348 |
| 825 | Nanjing Massacre | 77.5% | 88 | 0 of 6 | 1407 |
| 1105 | Lawrence Bishnoi | 77.4% | 40 | 1 of 5 | 682 |
| 883 | 1948 Palestinian exodus from Lydda and Ramle | 76.9% | 88 | 2 of 6 | 1432 |
| 577 | Commisso 'ndrina | 76.6% | 95 | 0 of 5 | 1550 |
| 850 | Dzyatlava massacre | 76.4% | 42 | 2 of 3 | 669 |
| 973 | Barrios Altos massacre | 76.0% | 53 | 1 of 4 | 891 |
| 807 | May Thirtieth Movement | 75.5% | 108 | 3 of 5 | 1718 |
| 574 | Bangkok | 75.4% | 97 | 3 of 5 | 1701 |
| 230 | Gregory Scarpa | 75.1% | 122 | 0 of 7 | 1998 |
| 885 | Safsaf massacre | 75.0% | 34 | 0 of 3 | 558 |
| 741 | Chios massacre | 74.9% | 55 | 1 of 5 | 916 |
| 965 | Milltown Cemetery attack | 74.8% | 71 | 4 of 5 | 1170 |
| 822 | Paracuellos massacres | 74.7% | 57 | 2 of 6 | 964 |
| 851 | Lidice massacre | 74.1% | 75 | 2 of 4 | 1285 |
| 821 | Gondrand massacre | 73.9% | 38 | 2 of 5 | 638 |
| 1084 | Road Rats Motorcycle Club | 73.5% | 36 | 0 of 4 | 638 |
| 1103 | Robb Elementary School shooting | 73.3% | 48 | 1 of 4 | 851 |
| 804 | Rosewood massacre | 72.7% | 138 | 1 of 6 | 2289 |
| 980 | La Cantuta massacre | 72.6% | 52 | 1 of 4 | 929 |
| 974 | Santa Cruz massacre | 72.5% | 55 | 2 of 4 | 974 |
| 239 | Rollin' 30s Harlem Crips | 72.4% | 49 | 0 of 3 | 823 |
| 856 | Massacre of the Acqui Division | 71.7% | 72 | 0 of 4 | 1246 |
| 846 | Frog Lake Massacre | 71.7% | 62 | 4 of 4 | 1100 |
| 848 | Parsley massacre | 71.6% | 51 | 4 of 4 | 889 |
| 420 | Peter Foster | 70.8% | 129 | 2 of 9 | 2295 |
| 935 | Kingsmill massacre | 70.1% | 76 | 1 of 4 | 1356 |
| 868 | Marzabotto massacre | 69.9% | 84 | 4 of 6 | 1534 |
| 278 | Triad (organized crime) | 69.8% | 78 | 5 of 6 | 1420 |
| 926 | Siege of Tel al-Zaatar | 69.3% | 41 | 2 of 4 | 758 |
| 1039 | 2019 El Paso shooting | 69.1% | 55 | 2 of 5 | 1032 |
| 976 | Bara massacre | 68.7% | 24 | 1 of 2 | 430 |
| 408 | Carl Williams (criminal) | 68.5% | 66 | 4 of 6 | 1202 |
| 1060 | Grim Reapers Motorcycle Club (USA) | 68.5% | 33 | 3 of 4 | 608 |
| 324 | Bandidos Motorcycle Club | 68.2% | 63 | 4 of 6 | 1129 |
| 763 | Mountain Meadows Massacre | 68.1% | 80 | 0 of 5 | 1467 |
| 853 | Khatyn massacre | 67.9% | 54 | 5 of 5 | 1023 |
| 762 | Crabb massacre | 67.8% | 52 | 2 of 4 | 962 |
| 722 | Massacre of Glencoe | 67.8% | 71 | 3 of 5 | 1324 |
| 1095 | Vendettas Motorcycle Club | 67.4% | 23 | 0 of 4 | 481 |
| 938 | Soweto uprising | 67.3% | 74 | 2 of 5 | 1436 |
| 291 | Black Guerrilla Family | 66.8% | 34 | 2 of 5 | 625 |
| 1040 | 2020 Lekki shooting | 66.7% | 63 | 2 of 5 | 1112 |
| 858 | Kalavryta massacre | 66.7% | 33 | 0 of 3 | 614 |
| 887 | Rengat massacre | 66.5% | 47 | 0 of 4 | 893 |
| 828 | Częstochowa massacre | 66.5% | 87 | 4 of 5 | 1662 |
| 482 | Paula Denyer | 66.2% | 96 | 3 of 10 | 1856 |
| 857 | Battle of Wake Island | 66.0% | 55 | 2 of 5 | 1100 |
| 992 | Loughinisland massacre | 65.9% | 53 | 1 of 4 | 1026 |
| 904 | Novocherkassk massacre | 65.7% | 92 | 0 of 5 | 1741 |
| 716 | Irish Rebellion of 1641 | 65.5% | 66 | 1 of 5 | 1321 |
| 849 | Kragujevac massacre | 65.4% | 55 | 3 of 4 | 1093 |
| 1096 | Warlocks Motorcycle Club (Pennsylvania) | 65.3% | 26 | 1 of 4 | 535 |
| 854 | Naliboki massacre | 65.0% | 47 | 3 of 3 | 899 |
| 881 | Kfar Etzion massacre | 64.8% | 51 | 0 of 4 | 1092 |
| 922 | Ezeiza massacre | 64.6% | 35 | 0 of 3 | 678 |
| 847 | Qissa Khwani massacre | 64.4% | 38 | 2 of 4 | 725 |
| 1099 | Ashfaqulla Khan | 64.1% | 30 | 2 of 4 | 588 |
| 1042 | La Vega raid | 64.1% | 31 | 1 of 4 | 643 |
| 1061 | Head Hunters Motorcycle Club | 64.1% | 42 | 2 of 4 | 815 |
| 502 | Derek Percy | 64.0% | 50 | 1 of 4 | 983 |
| 1038 | Sobane Da massacre | 63.9% | 25 | 2 of 3 | 457 |
| 542 | Domenico Libri | 63.9% | 34 | 2 of 5 | 672 |
| 442 | Michael Odisho | 63.6% | 62 | 0 of 7 | 1188 |
| 734 | September Massacres | 62.9% | 63 | 1 of 5 | 1261 |
| 1029 | 2014 Peshawar school massacre | 62.7% | 37 | 5 of 5 | 831 |
| 1030 | Charleston church shooting | 62.5% | 53 | 3 of 6 | 1153 |
| 1027 | 2014 Bentiu massacre | 62.4% | 30 | 4 of 4 | 622 |
| 845 | Massacre Canyon | 62.3% | 56 | 3 of 5 | 1161 |
| 334 | Mongrel Mob | 62.1% | 75 | 0 of 7 | 1509 |
| 311 | Greek mafia | 62.0% | 74 | 1 of 7 | 1471 |
| 1033 | Inn Din massacre | 61.6% | 53 | 6 of 6 | 1095 |
| 940 | 6 October 1976 massacre | 61.4% | 73 | 1 of 4 | 1490 |
| 1044 | Solhan and Tadaryat massacres | 61.4% | 35 | 2 of 4 | 751 |
| 963 | Remembrance Day bombing | 61.4% | 53 | 4 of 5 | 1211 |
| 1034 | Nduga massacre | 61.1% | 37 | 3 of 4 | 766 |
| 399 | Martin Stephens (drug smuggler) | 61.1% | 40 | 1 of 6 | 840 |
| 1051 | Brother Speed | 61.0% | 27 | 1 of 4 | 577 |
| 920 | Lod Airport massacre | 60.9% | 34 | 2 of 3 | 688 |
| 951 | Wah Mee massacre | 60.6% | 65 | 2 of 6 | 1447 |
| 775 | Thibodaux massacre | 60.3% | 56 | 0 of 5 | 1199 |
| 1037 | Ogossagou massacre | 60.2% | 30 | 3 of 3 | 621 |
| 330 | Hells Angels | 60.0% | 56 | 3 of 7 | 1225 |
| 833 | Gudovac massacre | 59.9% | 93 | 2 of 4 | 1888 |
| 1079 | Peckerwood | 59.7% | 31 | 0 of 4 | 708 |
| 967 | École Polytechnique massacre | 59.1% | 61 | 3 of 5 | 1436 |
| 810 | Bath School disaster | 58.8% | 63 | 0 of 4 | 1331 |
| 919 | Bloody Sunday (1972) | 58.7% | 45 | 1 of 5 | 1025 |
| 1035 | Christchurch mosque shootings | 58.6% | 46 | 1 of 5 | 1023 |
| 390 | David McMillan (smuggler) | 58.5% | 101 | 7 of 8 | 2238 |
| 1078 | Pagan's Motorcycle Club | 58.4% | 32 | 2 of 4 | 712 |
| 248 | Latin Kings (gang) | 58.4% | 49 | 3 of 7 | 1111 |
| 1097 | Warlocks Motorcycle Club (Florida) | 58.4% | 24 | 1 of 4 | 522 |
| 1036 | Kharqamar incident | 58.3% | 39 | 3 of 5 | 870 |
| 861 | Ardeatine massacre | 58.1% | 55 | 2 of 5 | 1209 |
| 1064 | Highway 61 Motorcycle Club | 58.0% | 23 | 1 of 3 | 474 |
| 796 | Elaine massacre | 57.6% | 77 | 1 of 6 | 1759 |
| 1062 | Hells Angels | 57.5% | 42 | 1 of 5 | 1018 |
| 972 | Somaliland War of Independence | 57.2% | 50 | 1 of 5 | 1147 |
| 812 | Banana Massacre | 57.2% | 46 | 1 of 5 | 1028 |
| 1032 | Orlando nightclub shooting | 56.8% | 41 | 4 of 6 | 1053 |
| 927 | Jeju uprising | 56.6% | 56 | 1 of 6 | 1231 |
| 924 | Ma'alot massacre | 56.5% | 41 | 1 of 3 | 935 |
| 884 | Babrra massacre | 56.4% | 28 | 2 of 3 | 684 |
| 910 | Asaba massacre | 56.4% | 38 | 2 of 5 | 874 |
| 930 | University of Texas tower shooting | 56.3% | 70 | 1 of 6 | 1654 |
| 1053 | Chicanos Motorcycle Club | 56.1% | 27 | 4 of 4 | 607 |
| 443 | Nik Radev | 55.9% | 26 | 0 of 5 | 613 |
| 844 | Cypress Hills Massacre | 55.7% | 44 | 1 of 4 | 1094 |
| 1082 | Rebels Motorcycle Club (Canada) | 55.5% | 22 | 0 of 4 | 567 |
| 508 | Geoffrey Edelsten | 55.0% | 52 | 1 of 6 | 1147 |
| 313 | Irish Mob | 54.8% | 96 | 0 of 9 | 2250 |
| 1041 | Axum massacre | 54.3% | 36 | 2 of 4 | 905 |
| 541 | Carmine Alfieri | 54.2% | 34 | 0 of 3 | 795 |
| 280 | Bamboo Union | 54.0% | 48 | 1 of 6 | 1197 |
| 1059 | Grim Reapers Motorcycle Club (Canada) | 53.4% | 16 | 0 of 3 | 428 |
| 789 | Ludlow Massacre | 53.4% | 73 | 2 of 6 | 1813 |
| 1063 | Hell's Lovers | 53.1% | 23 | 2 of 3 | 628 |
| 945 | Gwangju Uprising | 53.0% | 38 | 2 of 4 | 924 |
| 1028 | Sinjar massacre | 52.8% | 31 | 3 of 5 | 855 |
| 396 | Renae Lawrence | 52.6% | 41 | 4 of 7 | 1054 |
| 875 | Portella della Ginestra massacre | 52.6% | 35 | 1 of 4 | 901 |
| 1056 | Finks Motorcycle Club | 52.3% | 24 | 1 of 4 | 609 |
| 1049 | Blue Angels Motorcycle Club | 52.3% | 36 | 2 of 4 | 948 |
| 312 | Honoured Society (Australia) | 52.2% | 55 | 1 of 6 | 1324 |
| 393 | Gerald Ridsdale | 52.1% | 35 | 0 of 6 | 962 |
| 960 | Aranthalawa massacre | 51.9% | 35 | 1 of 4 | 831 |
| 779 | Port Arthur massacre (China) | 51.7% | 47 | 1 of 6 | 1192 |
| 880 | Hadassah medical convoy massacre | 51.4% | 33 | 1 of 3 | 882 |
| 1058 | Gremium Motorcycle Club | 51.4% | 13 | 2 of 3 | 396 |
| 937 | Damour massacre | 51.3% | 35 | 1 of 4 | 894 |
| 1081 | Popeye Moto Club | 51.3% | 30 | 1 of 4 | 750 |
| 781 | Battle of Mazocoba | 51.2% | 35 | 1 of 3 | 853 |
| 336 | Outlaws Motorcycle Club | 51.1% | 34 | 0 of 5 | 874 |
| 557 | Pietro Aglieri | 50.9% | 34 | 2 of 4 | 924 |
| 421 | Simon Hannes | 50.6% | 80 | 1 of 8 | 2078 |
| 498 | William MacDonald (serial killer) | 50.6% | 84 | 2 of 5 | 2140 |
| 979 | Boipatong massacre | 50.6% | 21 | 1 of 3 | 523 |
| 590 | Lima | 50.5% | 55 | 3 of 5 | 1444 |
| 588 | Giuseppe Coluccio | 50.5% | 28 | 1 of 4 | 750 |
| 929 | Sharpeville massacre | 50.3% | 33 | 3 of 5 | 891 |
| 271 | Shelltown, San Diego | 50.2% | 19 | 3 of 5 | 493 |
| 788 | Massacres of Albanians in the Balkan Wars | 50.0% | 67 | 0 of 7 | 1805 |
| 1123 | Murder of Logan Mwangi | 49.9% | 42 | 1 of 5 | 1086 |
| 451 | Bevan Spencer von Einem | 49.3% | 90 | 4 of 9 | 2435 |
| 497 | Berrima, New South Wales | 49.2% | 39 | 2 of 6 | 985 |
| 601 | Salvatore Miceli | 49.1% | 19 | 0 of 3 | 583 |
| 378 | Roman Catholic Diocese of Maitland-Newcastle | 49.1% | 47 | 3 of 4 | 1281 |
| 800 | Ocoee massacre | 49.0% | 63 | 3 of 6 | 1734 |
| 362 | Darcy Dugan | 48.9% | 20 | 0 of 4 | 502 |
| 331 | Red Devils Motorcycle Club | 48.8% | 28 | 1 of 4 | 786 |
| 955 | Dujail Massacre | 48.7% | 35 | 1 of 5 | 999 |
| 233 | Armenian Power | 48.6% | 28 | 0 of 4 | 740 |
| 325 | Diablos Motorcycle Club (founded 1999) | 48.5% | 27 | 0 of 5 | 741 |
| 925 | Maratha, Santalaris and Aloda massacre | 48.5% | 22 | 2 of 3 | 531 |
| 961 | Pınarcık massacre | 48.3% | 29 | 0 of 4 | 784 |
| 1086 | Rock Machine | 48.1% | 23 | 1 of 4 | 733 |
| 1072 | Mongols Motorcycle Club | 47.7% | 40 | 2 of 6 | 1151 |
| 290 | Aryan Brotherhood | 47.7% | 50 | 2 of 6 | 1526 |
| 494 | Thomas Jeffries | 47.5% | 73 | 0 of 9 | 2112 |
| 713 | Siege of Smerwick | 47.5% | 41 | 1 of 4 | 1123 |
| 476 | David and Catherine Birnie | 47.3% | 54 | 1 of 7 | 1607 |
| 918 | El Halconazo | 47.1% | 26 | 2 of 3 | 705 |
| 826 | Tsuyama massacre | 47.0% | 42 | 3 of 4 | 1144 |
| 832 | Fântâna Albă massacre | 47.0% | 42 | 2 of 5 | 1206 |
| 285 | Wo Shing Wo | 46.9% | 48 | 1 of 6 | 1445 |
| 958 | 1987 Lieyu massacre | 46.8% | 45 | 1 of 4 | 1254 |
| 944 | Greensboro massacre | 46.5% | 42 | 0 of 5 | 1215 |
| 921 | Munich massacre | 46.4% | 41 | 1 of 5 | 1178 |
| 480 | Eric Edgar Cooke | 46.4% | 48 | 5 of 8 | 1411 |
| 570 | Francesco Mallardo | 46.3% | 32 | 0 of 5 | 927 |
| 284 | Wah Ching | 46.2% | 49 | 1 of 7 | 1338 |
| 1092 | Sons of Silence | 46.2% | 20 | 0 of 4 | 635 |
| 1008 | Nepalese royal massacre | 46.1% | 31 | 2 of 4 | 858 |
| 748 | Myall Creek massacre | 45.9% | 53 | 0 of 4 | 1520 |
| 469 | Neddy Smith | 45.5% | 34 | 2 of 6 | 948 |
| 475 | David and Catherine Birnie | 45.5% | 51 | 1 of 5 | 1554 |
| 840 | Rumbula massacre | 45.1% | 62 | 2 of 7 | 1903 |
| 1112 | Assassination of Shinzo Abe | 45.1% | 47 | 1 of 8 | 1438 |
| 838 | Babi Yar | 45.0% | 38 | 4 of 6 | 1195 |
| 839 | Ninth Fort massacres of November 1941 | 44.9% | 46 | 2 of 5 | 1230 |
| 405 | Roger Rogerson | 44.5% | 41 | 3 of 7 | 1250 |
| 1031 | November 2015 Paris attacks | 44.5% | 36 | 3 of 4 | 1142 |
| 686 | Hazaras | 44.4% | 53 | 0 of 6 | 1647 |
| 993 | 1994 Mokokchung Massacre | 43.7% | 16 | 2 of 3 | 540 |
| 760 | Goliad massacre | 43.6% | 36 | 1 of 5 | 1094 |
| 809 | Columbine Mine massacre | 43.5% | 41 | 1 of 4 | 1160 |
| 879 | Deir Yassin massacre | 43.4% | 25 | 0 of 4 | 788 |
| 785 | 1906 Atlanta race riot | 43.4% | 34 | 1 of 5 | 1104 |
| 947 | El Mozote massacre | 43.4% | 30 | 3 of 5 | 946 |
| 1052 | Cannonball Motorcycle Club | 43.2% | 24 | 2 of 4 | 744 |
| 994 | Beit Lid suicide bombing | 43.0% | 25 | 3 of 4 | 721 |
| 835 | Massacre of Kondomari | 42.9% | 45 | 1 of 6 | 1305 |
| 1014 | Haditha massacre | 42.6% | 28 | 3 of 5 | 960 |
| 517 | Harshad Mehta | 42.6% | 38 | 1 of 6 | 1230 |
| 949 | Sabra and Shatila massacre | 42.0% | 26 | 1 of 3 | 843 |
| 1048 | Black Pistons Motorcycle Club | 42.0% | 17 | 3 of 4 | 610 |
| 438 | John 'Chow' Hayes | 42.0% | 20 | 1 of 5 | 684 |
| 288 | 14K (triad) | 41.9% | 34 | 3 of 5 | 1144 |
| 418 | Premier of Western Australia | 41.7% | 27 | 0 of 6 | 942 |
| 778 | Hamidian massacres | 41.7% | 37 | 2 of 5 | 1315 |
| 406 | Robert Trimbole | 41.6% | 35 | 4 of 7 | 1098 |
| 957 | Accomarca massacre | 41.6% | 23 | 0 of 3 | 683 |
| 882 | Tantura massacre | 41.6% | 43 | 1 of 6 | 1470 |
| 964 | Queen Street massacre | 41.6% | 26 | 0 of 4 | 929 |
| 692 | Kabul | 41.3% | 48 | 0 of 6 | 1813 |
| 784 | First Battle of Bud Dajo | 41.2% | 42 | 0 of 5 | 1392 |
| 959 | 1987 Lieyu massacre | 41.2% | 40 | 1 of 4 | 1234 |
| 607 | Pasquale Russo | 41.0% | 16 | 1 of 4 | 564 |
| 274 | Venice 13 | 40.7% | 31 | 1 of 4 | 1058 |
| 888 | Mungyeong massacre | 40.6% | 13 | 0 of 2 | 397 |
| 907 | Indonesian mass killings of 1965–66 | 40.5% | 29 | 0 of 4 | 1041 |
| 495 | Eddie Leonski | 40.5% | 24 | 4 of 6 | 875 |
| 356 | Milton Orkopoulos | 40.3% | 20 | 0 of 5 | 836 |
| 1111 | Robert Eugene Brashers | 40.1% | 46 | 2 of 8 | 1738 |
| 758 | Baylor Massacre | 40.1% | 17 | 2 of 5 | 641 |
| 361 | Brenden Abbott | 40.1% | 29 | 1 of 6 | 1138 |
| 859 | Foibe massacres | 40.0% | 23 | 1 of 3 | 695 |
| 273 | Varrio Nuevo Estrada | 40.0% | 19 | 2 of 4 | 615 |
| 626 | 2011 Inter-Continental Hotel Kabul attack | 40.0% | 24 | 1 of 5 | 816 |
| 329 | Finks Motorcycle Club | 39.9% | 15 | 1 of 3 | 506 |
| 377 | Patrick Power (lawyer) | 39.1% | 14 | 1 of 5 | 618 |
| 373 | Brothers Hospitallers of Saint John of God | 38.8% | 34 | 0 of 5 | 1205 |
| 276 | Vineland Boys | 38.6% | 12 | 1 of 3 | 427 |
| 244 | Fresno Bulldogs | 38.5% | 25 | 5 of 5 | 844 |
| 314 | Moran family | 38.5% | 29 | 0 of 5 | 957 |
| 752 | Pottawatomie massacre | 38.4% | 28 | 1 of 4 | 965 |
| 576 | Salvatore Riina | 38.4% | 44 | 1 of 8 | 1948 |
| 874 | February 28 incident | 38.4% | 24 | 0 of 5 | 938 |
| 1127 | Jussie Smollett | 38.2% | 21 | 1 of 5 | 779 |
| 485 | Claremont, Western Australia | 38.1% | 25 | 6 of 7 | 982 |
| 327 | Coffin Cheaters | 38.1% | 13 | 1 of 3 | 520 |
| 562 | Giuseppe Piromalli (born 1945) | 38.0% | 27 | 2 of 5 | 1000 |
| 1080 | Pissed Off Bastards of Bloomington | 37.9% | 14 | 0 of 4 | 565 |
| 684 | Marshal Fahim National Defense University | 37.7% | 33 | 2 of 8 | 1302 |
| 298 | Diablos Motorcycle Club | 37.7% | 25 | 2 of 5 | 1006 |
| 430 | Glenn Wheatley | 37.5% | 29 | 1 of 6 | 1153 |
| 708 | Brussels massacre | 37.4% | 29 | 1 of 5 | 1014 |
| 890 | Seoul National University Hospital massacre | 37.4% | 10 | 0 of 3 | 369 |
| 1013 | Andijan massacre | 37.3% | 24 | 0 of 5 | 967 |
| 950 | Dos Erres massacre | 37.3% | 33 | 1 of 4 | 1193 |
| 477 | Gregory Brazel | 37.2% | 18 | 2 of 4 | 639 |
| 367 | Brett Peter Cowan | 37.2% | 31 | 0 of 6 | 1291 |
| 513 | Mehul Choksi | 37.0% | 23 | 0 of 5 | 815 |
| 305 | Albanian mafia | 37.0% | 21 | 0 of 6 | 907 |
| 267 | OVS (gang) | 36.6% | 21 | 0 of 5 | 866 |
| 256 | Public Enemy No. 1 (gang) | 36.5% | 15 | 2 of 4 | 545 |
| 906 | Palma Sola massacre | 36.4% | 17 | 0 of 4 | 650 |
| 836 | NKVD prisoner massacres | 36.3% | 30 | 1 of 3 | 1095 |
| 1100 | Solomon Molcho | 36.0% | 17 | 0 of 5 | 735 |
| 479 | Robert Francis Burns | 35.9% | 51 | 0 of 7 | 1993 |
| 876 | Rawagede massacre | 35.9% | 20 | 2 of 5 | 755 |
| 381 | Bali Nine | 35.7% | 19 | 1 of 6 | 845 |
| 460 | Martin Leach (murderer) | 35.6% | 19 | 2 of 6 | 857 |
| 751 | Haun's Mill massacre | 35.5% | 26 | 3 of 5 | 1072 |
| 358 | Karyn Paluzzano | 35.4% | 17 | 0 of 5 | 748 |
| 372 | Brian Keith Jones | 35.3% | 21 | 0 of 4 | 741 |
| 560 | Buenos Aires | 35.2% | 30 | 5 of 6 | 1255 |
| 902 | Matikhrü massacre | 35.1% | 23 | 1 of 5 | 884 |
| 956 | Anuradhapura massacre | 35.1% | 22 | 1 of 4 | 793 |
| 820 | Ranquil massacre | 35.0% | 16 | 1 of 5 | 725 |
| 279 | 14K (triad) | 35.0% | 32 | 2 of 5 | 1358 |
| 803 | Perry massacre | 34.8% | 19 | 0 of 3 | 773 |
| 359 | Adam Marshall | 34.5% | 25 | 0 of 6 | 937 |
| 842 | Battle of Ambon | 34.5% | 27 | 2 of 4 | 1094 |
| 1009 | Osaka school massacre | 34.3% | 15 | 2 of 4 | 783 |
| 934 | Miami Showband killings | 34.2% | 26 | 1 of 4 | 1076 |
| 829 | Katyn massacre | 34.1% | 29 | 2 of 5 | 1424 |
| 571 | Giuseppe Morabito | 34.0% | 25 | 0 of 5 | 1001 |
| 913 | Phong Nhị and Phong Nhất massacre | 34.0% | 19 | 0 of 3 | 760 |
| 332 | Highway 61 Motorcycle Club | 33.8% | 16 | 0 of 3 | 528 |
| 369 | Dennis Ferguson | 33.8% | 28 | 3 of 6 | 1312 |
| 873 | Sétif and Guelma massacre | 33.5% | 19 | 1 of 5 | 991 |
| 464 | Bradley John Murdoch | 33.4% | 24 | 1 of 6 | 1114 |
| 355 | Phuong Ngo | 33.3% | 29 | 0 of 6 | 1237 |
| 449 | Martin Bryant | 33.2% | 24 | 3 of 7 | 1068 |
| 595 | Giuseppe Setola | 33.2% | 23 | 1 of 4 | 943 |
| 802 | Tulsa race massacre | 32.7% | 28 | 0 of 6 | 1344 |
| 346 | Andrew Theophanous | 32.7% | 24 | 2 of 6 | 1020 |
| 415 | Hajnal Ban | 32.7% | 23 | 1 of 5 | 1081 |
| 618 | Uzbin Valley ambush | 32.5% | 39 | 1 of 5 | 1579 |
| 843 | Sook Ching | 32.3% | 15 | 2 of 3 | 792 |
| 478 | Snowtown murders | 32.2% | 22 | 4 of 5 | 1008 |
| 841 | Arakan massacres in 1942 | 32.1% | 15 | 2 of 4 | 643 |
| 622 | 2009 UN guest house attack in Kabul | 32.1% | 15 | 3 of 4 | 597 |
| 867 | Ochota massacre | 31.8% | 14 | 2 of 4 | 704 |
| 323 | Lebanese mafia | 31.5% | 24 | 0 of 6 | 1064 |
| 575 | Ústí nad Labem | 31.5% | 21 | 1 of 8 | 1076 |
| 1089 | Satudarah | 31.4% | 14 | 0 of 5 | 692 |
| 437 | Mick Gatto | 31.2% | 15 | 3 of 5 | 781 |
| 1025 | Houla massacre | 30.9% | 18 | 1 of 5 | 933 |
| 917 | Ketnar Bil massacre | 30.9% | 12 | 1 of 3 | 565 |
| 434 | Mallorca | 30.9% | 24 | 4 of 5 | 1272 |
| 1075 | Notorious Motorcycle Club (Australia) | 30.8% | 14 | 0 of 4 | 683 |
| 805 | Kantō Massacre | 30.5% | 33 | 0 of 6 | 1383 |
| 801 | Bloody Sunday (1920) | 30.4% | 25 | 0 of 5 | 1390 |
| 878 | Balad al-Shaykh massacre | 30.3% | 14 | 1 of 4 | 688 |
| 257 | Satanas (gang) | 30.3% | 11 | 1 of 3 | 583 |
| 468 | Joseph Schwab | 30.2% | 17 | 2 of 5 | 885 |
| 808 | Shanghai massacre | 30.2% | 28 | 2 of 4 | 1214 |
| 572 | Madrid | 30.2% | 37 | 1 of 5 | 1924 |
| 333 | Loners Motorcycle Club | 30.2% | 21 | 1 of 5 | 1175 |
| 914 | Hà My massacre | 30.1% | 12 | 0 of 3 | 573 |
| 237 | Asian Boyz | 30.1% | 30 | 0 of 6 | 1329 |
| 916 | Kent State shootings | 30.0% | 18 | 2 of 4 | 914 |
| 363 | Keith Faure | 29.8% | 17 | 1 of 5 | 882 |
| 592 | Vincenzo Licciardi | 29.6% | 17 | 0 of 6 | 889 |
| 391 | Van Tuong Nguyen | 29.5% | 26 | 1 of 7 | 1427 |
| 507 | Truro murders | 29.5% | 25 | 0 of 5 | 1159 |
| 402 | Thailand | 29.3% | 21 | 5 of 7 | 1084 |
| 827 | Seguro Obrero massacre | 29.3% | 19 | 1 of 4 | 985 |
| 458 | Katherine Knight | 29.2% | 30 | 3 of 6 | 1561 |
| 900 | Kafr Qasim massacre | 29.1% | 24 | 1 of 4 | 1097 |
| 1110 | Highland Park parade shooting | 29.1% | 24 | 2 of 7 | 1225 |
| 1012 | Beslan school siege | 28.8% | 22 | 1 of 5 | 1156 |
| 777 | Massacre of Italians at Aigues-Mortes | 28.7% | 21 | 0 of 4 | 1006 |
| 824 | Dersim rebellion | 28.7% | 36 | 3 of 7 | 1844 |
| 398 | Scott Rush | 28.5% | 26 | 2 of 8 | 1273 |
| 258 | Sinaloa Cartel | 28.5% | 25 | 0 of 6 | 1315 |
| 496 | John Lynch (serial killer) | 28.5% | 32 | 1 of 8 | 1683 |
| 488 | Rockhampton | 28.4% | 27 | 0 of 10 | 1423 |
| 1026 | August 2013 Rabaa massacre | 28.4% | 16 | 3 of 5 | 871 |
| 712 | Clandeboye massacre | 28.3% | 14 | 2 of 4 | 873 |
| 439 | Michael Kanaan | 28.2% | 30 | 3 of 11 | 1683 |
| 275 | White Fence | 28.1% | 9 | 1 of 3 | 524 |
| 877 | Haifa Oil Refinery massacre | 28.1% | 10 | 0 of 4 | 670 |
| 923 | Battle of Jolo (1974) | 27.9% | 14 | 1 of 4 | 715 |
| 489 | John Wayne Glover | 27.9% | 29 | 4 of 7 | 1913 |
| 435 | George Freeman (bookmaker) | 27.9% | 11 | 1 of 6 | 647 |
| 347 | Craig Thomson (politician) | 27.9% | 26 | 4 of 8 | 1308 |
| 855 | Dominopol massacre | 27.8% | 11 | 1 of 3 | 577 |
| 691 | Jowzjan Province | 27.8% | 19 | 0 of 5 | 1016 |
| 954 | 1984 anti-Sikh riots | 27.7% | 19 | 0 of 4 | 1126 |
| 1067 | Iron Order Motorcycle Club | 27.6% | 14 | 0 of 4 | 812 |
| 445 | Squizzy Taylor | 27.5% | 24 | 2 of 5 | 1427 |
| 757 | Lachine massacre | 27.4% | 20 | 1 of 4 | 1120 |
| 915 | Mỹ Lai massacre | 27.2% | 17 | 2 of 6 | 1148 |
| 724 | Penn's Creek massacre | 27.0% | 34 | 1 of 5 | 1791 |
| 819 | Simele massacre | 27.0% | 40 | 3 of 5 | 2355 |
| 255 | Nazi Lowriders | 27.0% | 14 | 1 of 5 | 929 |
| 1011 | Passover massacre | 26.8% | 13 | 1 of 5 | 728 |
| 428 | Christopher Skase | 26.6% | 17 | 1 of 5 | 972 |
| 251 | Mongols Motorcycle Club | 26.5% | 20 | 0 of 7 | 1090 |
| 286 | Vagos Motorcycle Club | 26.5% | 19 | 0 of 5 | 1204 |
| 1004 | Omagh bombing | 26.5% | 16 | 1 of 5 | 851 |
| 326 | Club Deroes | 26.4% | 9 | 1 of 2 | 437 |
| 457 | Hoddle Street massacre | 26.3% | 32 | 3 of 9 | 2014 |
| 505 | Lindsey Robert Rose | 26.2% | 19 | 3 of 6 | 1060 |
| 481 | Bandali Debs | 25.9% | 9 | 2 of 5 | 680 |
| 1090 | Sin City Deciples Motorcycle Club | 25.9% | 7 | 1 of 4 | 585 |
| 253 | Norteños | 25.7% | 16 | 0 of 6 | 879 |
| 1047 | Bacchus Motorcycle Club | 25.7% | 12 | 2 of 4 | 834 |
| 348 | Steve Irons | 25.7% | 16 | 3 of 7 | 972 |
| 933 | Palimbang massacre | 25.6% | 17 | 1 of 5 | 927 |
| 1077 | Original Red Devils Motorcycle Club | 25.5% | 9 | 0 of 3 | 526 |
| 815 | 1929 Palestine riots | 25.3% | 25 | 0 of 5 | 1410 |
| 587 | Edoardo Contini | 25.3% | 14 | 3 of 5 | 803 |
| 470 | Murder of Anita Cobby | 25.2% | 22 | 2 of 6 | 1423 |
| 765 | Nueces massacre | 25.2% | 13 | 1 of 4 | 798 |
| 870 | Chenogne massacre | 24.9% | 10 | 1 of 4 | 600 |
| 564 | Luigi Giuliano | 24.8% | 18 | 1 of 5 | 964 |
| 547 | Pasquale Condello | 24.7% | 16 | 4 of 5 | 886 |
| 787 | Adana massacre | 24.6% | 17 | 0 of 5 | 1110 |
| 966 | 1989 Tiananmen Square protests and massacre | 24.6% | 17 | 1 of 5 | 1241 |
| 462 | Craig Minogue | 24.6% | 14 | 2 of 6 | 900 |
| 436 | Alphonse Gangitano | 24.5% | 7 | 3 of 5 | 519 |
| 297 | Pirus | 24.4% | 14 | 0 of 5 | 852 |
| 322 | DLASTHR | 24.4% | 23 | 0 of 7 | 1602 |
| 265 | Florencia 13 | 24.4% | 13 | 1 of 5 | 789 |
| 761 | 1842 retreat from Kabul | 24.3% | 18 | 3 of 6 | 1417 |
| 817 | Zilan massacre | 24.3% | 15 | 3 of 5 | 1063 |
| 308 | Barbaro 'ndrina | 24.3% | 21 | 0 of 6 | 1459 |
| 252 | MS-13 | 24.2% | 18 | 0 of 8 | 1358 |
| 1083 | Road Knights | 24.2% | 6 | 0 of 4 | 434 |
| 379 | Peter Scully | 24.2% | 18 | 0 of 6 | 1111 |
| 596 | Raffaele Diana | 24.2% | 7 | 0 of 3 | 554 |
| 823 | Ponce massacre | 24.2% | 20 | 2 of 6 | 1308 |
| 512 | Amit Bhardwaj | 24.1% | 10 | 1 of 5 | 553 |
| 232 | Mexican Mafia | 23.9% | 16 | 1 of 5 | 1133 |
| 413 | Rodney Adler | 23.9% | 18 | 1 of 6 | 1190 |
| 903 | Paris massacre of 1961 | 23.9% | 17 | 0 of 5 | 1077 |
| 1055 | El Forastero Motorcycle Club | 23.9% | 4 | 1 of 3 | 294 |
| 1066 | Iron Horsemen | 23.8% | 11 | 0 of 3 | 563 |
| 872 | Bleiburg repatriations | 23.8% | 12 | 0 of 4 | 819 |
| 277 | Tiny Rascal Gang | 23.8% | 21 | 0 of 6 | 1211 |
| 737 | 1804 Haiti massacre | 23.7% | 22 | 1 of 7 | 1591 |
| 474 | Bilal Skaf | 23.6% | 9 | 4 of 5 | 717 |
| 1050 | Breed Motorcycle Club | 23.5% | 10 | 0 of 4 | 712 |
| 387 | Warren Fellows | 23.4% | 13 | 0 of 6 | 864 |
| 744 | Cutthroat Gap massacre | 23.4% | 20 | 2 of 6 | 1105 |
| 617 | 2008 bombing of Indian embassy in Kabul | 23.2% | 18 | 3 of 5 | 1093 |
| 247 | Hells Angels | 23.2% | 23 | 0 of 8 | 1612 |
| 685 | Kabul University | 23.1% | 24 | 0 of 6 | 1508 |
| 341 | Wilson Tuckey | 23.1% | 16 | 0 of 6 | 1175 |
| 409 | Jason Moran (criminal) | 23.0% | 22 | 1 of 4 | 1155 |
| 1007 | Dolphinarium discotheque massacre | 22.9% | 9 | 1 of 4 | 620 |
| 593 | Patrizio Bosti | 22.8% | 11 | 1 of 5 | 833 |
| 246 | Gypsy Joker Motorcycle Club | 22.8% | 15 | 3 of 4 | 1301 |
| 871 | Manila massacre | 22.7% | 11 | 0 of 5 | 856 |
| 768 | Battle of Fort Pillow | 22.7% | 19 | 2 of 5 | 1421 |
| 310 | The Carlton Crew | 22.6% | 14 | 0 of 4 | 849 |
| 742 | Naousa massacre | 22.3% | 8 | 0 of 4 | 687 |
| 869 | Malmedy massacre | 22.2% | 12 | 0 of 5 | 849 |
| 309 | Bulgarian mafia | 22.0% | 16 | 1 of 5 | 1040 |
| 932 | Kiryat Shmona massacre | 22.0% | 13 | 2 of 4 | 768 |
| 266 | Logan Heights Gang | 22.0% | 11 | 0 of 5 | 714 |
| 928 | Namyangju massacre | 22.0% | 8 | 0 of 3 | 474 |
| 243 | Devils Diciples | 21.9% | 11 | 0 of 4 | 871 |
| 1005 | Blue Market massacre | 21.8% | 8 | 1 of 4 | 532 |
| 260 | 38th Street gang | 21.8% | 13 | 1 of 5 | 851 |
| 431 | Ray Williams (businessman) | 21.7% | 9 | 0 of 4 | 594 |
| 738 | Fort Mims massacre | 21.7% | 14 | 0 of 3 | 1005 |
| 941 | Golden Dragon massacre | 21.7% | 9 | 1 of 4 | 835 |
| 338 | 2021 Lynchings for sacrilege in Punjab | 21.6% | 10 | 0 of 4 | 606 |
| 295 | Bahala Na Gang | 21.6% | 13 | 4 of 5 | 1024 |
| 764 | Gallinas massacre | 21.5% | 8 | 0 of 2 | 507 |
| 388 | Jim Krakouer | 21.5% | 18 | 2 of 6 | 1196 |
| 287 | Yakuza | 21.3% | 16 | 3 of 6 | 1270 |
| 682 | Lashkargah | 21.1% | 18 | 10 of 11 | 1480 |
| 1003 | Acteal massacre | 21.0% | 12 | 0 of 5 | 775 |
| 452 | Christopher Dale Flannery | 20.9% | 14 | 4 of 6 | 1123 |
| 700 | Battle of Changping | 20.9% | 19 | 1 of 6 | 1598 |
| 395 | Si Yi Chen | 20.9% | 11 | 1 of 6 | 896 |
| 710 | Battle of Cajamarca | 20.8% | 19 | 0 of 5 | 1420 |
| 594 | Girona | 20.6% | 26 | 2 of 5 | 2074 |
| 272 | Toonerville Rifa 13 | 20.6% | 8 | 0 of 4 | 573 |
| 486 | Perth | 20.5% | 9 | 1 of 4 | 813 |
| 718 | Storming of Bolton | 20.5% | 9 | 1 of 6 | 1059 |
| 328 | Comanchero Motorcycle Club | 20.3% | 12 | 0 of 6 | 967 |
| 447 | Dante Arthurs | 19.9% | 17 | 2 of 7 | 1480 |
| 731 | Pyle's Massacre | 19.8% | 11 | 1 of 3 | 974 |
| 472 | Peter Dupas | 19.8% | 11 | 0 of 6 | 1360 |
| 773 | Los Angeles Chinese massacre of 1871 | 19.7% | 10 | 1 of 4 | 1034 |
| 240 | Rollin' 60s Neighborhood Crips | 19.6% | 9 | 3 of 3 | 786 |
| 461 | Francesco Mangione | 19.6% | 7 | 1 of 4 | 531 |
| 693 | National Directorate of Security | 19.4% | 13 | 0 of 6 | 1193 |
| 714 | Junkersdorf massacre | 19.4% | 11 | 1 of 4 | 731 |
| 493 | Wagga Wagga | 19.4% | 13 | 1 of 4 | 1209 |
| 1023 | Mekong River massacre | 19.4% | 7 | 2 of 5 | 749 |
| 351 | Francis Abigail | 19.3% | 7 | 0 of 5 | 586 |
| 352 | Thomas Ley | 19.1% | 13 | 0 of 5 | 994 |
| 1010 | Gulbarg Society massacre | 19.0% | 8 | 1 of 4 | 757 |
| 831 | Ip massacre | 19.0% | 11 | 1 of 4 | 815 |
| 726 | Boston Massacre | 19.0% | 16 | 1 of 5 | 1485 |
| 834 | Glina massacres | 19.0% | 17 | 0 of 5 | 1603 |
| 677 | 2008 bombing of Indian embassy in Kabul | 18.9% | 11 | 2 of 6 | 1129 |
| 647 | March 2017 Kabul attack | 18.9% | 8 | 1 of 4 | 769 |
| 759 | Peterloo Massacre | 18.8% | 10 | 0 of 4 | 855 |
| 463 | Russell Street bombing | 18.8% | 11 | 3 of 5 | 1237 |
| 837 | Kamianets-Podilskyi massacre | 18.7% | 12 | 0 of 4 | 1005 |
| 1076 | Notorious Motorcycle Club (Germany) | 18.7% | 5 | 0 of 3 | 406 |
| 573 | Vito Roberto Palazzolo | 18.7% | 8 | 0 of 7 | 936 |
| 385 | Myuran Sukumaran | 18.7% | 15 | 1 of 8 | 1291 |
| 696 | 2021 Kabul airport attack | 18.6% | 11 | 3 of 6 | 1169 |
| 419 | Laurie Connell | 18.6% | 10 | 1 of 4 | 834 |
| 241 | Sons of Samoa | 18.6% | 5 | 2 of 3 | 495 |
| 962 | Hungerford massacre | 18.5% | 14 | 3 of 4 | 1570 |
| 988 | Saint James Church massacre | 18.4% | 9 | 1 of 4 | 764 |
| 321 | Hammerskins | 18.4% | 10 | 0 of 5 | 895 |
| 236 | Crips | 18.4% | 16 | 0 of 8 | 1503 |
| 813 | Saint Valentine's Day Massacre | 18.3% | 11 | 2 of 5 | 1301 |
| 912 | Jabidah massacre | 18.3% | 8 | 0 of 4 | 732 |
| 550 | Benedetto Santapaola | 18.2% | 19 | 2 of 7 | 1479 |
| 261 | Avenues (gang) | 18.2% | 9 | 0 of 6 | 741 |
| 456 | Julian Knight (murderer) | 18.2% | 10 | 3 of 7 | 1100 |
| 897 | Geochang massacre | 18.1% | 12 | 0 of 5 | 887 |
| 459 | Keli Lane | 17.9% | 13 | 0 of 7 | 1388 |
| 269 | Santa Monica 13 | 17.9% | 6 | 2 of 4 | 557 |
| 307 | Australian Defence League | 17.9% | 8 | 0 of 4 | 708 |
| 354 | Barry Morris | 17.9% | 11 | 0 of 5 | 917 |
| 315 | 'Ndrangheta | 17.8% | 11 | 0 of 4 | 847 |
| 235 | Chosen Few Motorcycle Club | 17.8% | 5 | 0 of 3 | 422 |
| 749 | Battle of Bad Axe | 17.7% | 13 | 1 of 6 | 1370 |
| 465 | Murder of Peter Falconio | 17.7% | 14 | 3 of 6 | 1749 |
| 899 | Qibya massacre | 17.6% | 9 | 2 of 5 | 988 |
| 602 | Taormina | 17.6% | 14 | 3 of 5 | 1233 |
| 292 | Nuestra Familia | 17.4% | 11 | 3 of 7 | 1117 |
| 487 | Leonard Fraser | 17.4% | 11 | 1 of 6 | 875 |
| 770 | American Ranch massacre | 17.3% | 8 | 0 of 3 | 691 |
| 1000 | El Aro Massacre | 17.3% | 9 | 0 of 4 | 698 |
| 417 | Brian Burke (Australian politician) | 17.2% | 11 | 1 of 6 | 1182 |
| 709 | Lisbon massacre | 17.2% | 10 | 1 of 4 | 1086 |
| 407 | Andrew Veniamin | 17.2% | 13 | 1 of 4 | 1133 |
| 783 | Leliefontein massacre | 17.2% | 5 | 1 of 4 | 692 |
| 340 | Benjamin Benny | 17.2% | 10 | 0 of 6 | 768 |
| 619 | February 2009 raids on Kabul | 17.2% | 6 | 0 of 4 | 553 |
| 484 | Claremont serial killings | 17.1% | 16 | 3 of 7 | 1674 |
| 725 | Massacre of St George's Fields | 17.1% | 12 | 1 of 4 | 1063 |
| 735 | First Massacre of Machecoul | 17.0% | 11 | 0 of 4 | 1070 |
| 999 | Ghulja incident | 17.0% | 8 | 2 of 4 | 646 |
| 756 | Lamey Island Massacre | 17.0% | 7 | 0 of 4 | 705 |
| 942 | Coastal Road massacre | 17.0% | 5 | 1 of 4 | 782 |
| 453 | Sef Gonzales | 16.8% | 12 | 2 of 6 | 1492 |
| 943 | Marichjhapi massacre | 16.6% | 6 | 0 of 4 | 841 |
| 695 | Bismillah Khan Mohammadi | 16.6% | 10 | 2 of 7 | 986 |
| 1001 | Luxor massacre | 16.6% | 8 | 1 of 4 | 721 |
| 360 | Eddie Obeid | 16.6% | 13 | 1 of 7 | 1368 |
| 818 | La Matanza | 16.4% | 12 | 3 of 5 | 1257 |
| 683 | Supreme Court of the Islamic Emirate of Afghanistan | 16.4% | 11 | 0 of 5 | 982 |
| 665 | May 2020 Afghanistan attacks | 16.3% | 11 | 1 of 6 | 1435 |
| 304 | Sun Yee On | 16.1% | 9 | 1 of 5 | 866 |
| 616 | 2008 Kabul Serena Hotel attack | 16.1% | 9 | 0 of 5 | 1041 |
| 599 | Antonio Pelle | 16.1% | 12 | 1 of 6 | 1198 |
| 936 | Karantina massacre | 15.9% | 11 | 0 of 3 | 741 |
| 501 | Martha Needle | 15.8% | 7 | 2 of 5 | 857 |
| 370 | Rolf Harris | 15.8% | 10 | 0 of 5 | 943 |
| 441 | Tony Mokbel | 15.7% | 9 | 1 of 5 | 1187 |
| 625 | May 2010 Kabul bombing | 15.7% | 6 | 4 of 5 | 813 |
| 549 | Umberto Ammaturo | 15.6% | 13 | 3 of 5 | 1332 |
| 319 | Serbian mafia | 15.4% | 9 | 0 of 5 | 1238 |
| 353 | Rex Jackson | 15.3% | 8 | 0 of 5 | 846 |
| 234 | Bounty Hunter Watts Bloods | 15.3% | 11 | 0 of 5 | 994 |
| 939 | Letipea massacre | 15.2% | 5 | 0 of 2 | 506 |
| 786 | Santa María School massacre | 15.1% | 14 | 1 of 7 | 1514 |
| 905 | Oran massacre of 1962 | 15.1% | 5 | 1 of 4 | 741 |
| 866 | Sant'Anna di Stazzema massacre | 14.9% | 3 | 1 of 3 | 623 |
| 1024 | Kandahar massacre | 14.8% | 4 | 1 of 6 | 987 |
| 386 | Schapelle Corby | 14.7% | 12 | 2 of 8 | 1502 |
| 433 | Woolworths Supermarkets | 14.7% | 7 | 0 of 5 | 904 |
| 349 | Peter Howe (New South Wales politician) | 14.7% | 6 | 3 of 6 | 843 |
| 701 | Invasion of Banu Qurayza | 14.6% | 8 | 1 of 5 | 1228 |
| 427 | Rene Rivkin | 14.6% | 7 | 1 of 5 | 921 |
| 371 | Robert Hughes (actor) | 14.6% | 6 | 0 of 5 | 948 |
| 745 | Dade battle | 14.6% | 13 | 1 of 6 | 1330 |
| 510 | David Hicks | 14.5% | 5 | 1 of 5 | 1037 |
| 646 | January 2017 Afghanistan bombings | 14.5% | 3 | 1 of 4 | 536 |
| 989 | Greysteel massacre | 14.5% | 6 | 1 of 4 | 710 |
| 641 | April 2016 Kabul attack | 14.4% | 6 | 1 of 4 | 735 |
| 281 | Big Circle Gang | 14.2% | 8 | 0 of 5 | 1032 |
| 648 | May 2017 Kabul bombing | 14.2% | 5 | 3 of 4 | 849 |
| 545 | Raffaele Cutolo | 14.2% | 12 | 3 of 7 | 1584 |
| 259 | Beltrán-Leyva Organization | 14.2% | 11 | 0 of 7 | 1154 |
| 953 | Wagalla massacre | 14.1% | 7 | 0 of 4 | 961 |
| 1019 | 2009 Fort Hood shooting | 14.0% | 6 | 1 of 4 | 705 |
| 750 | Killough massacre | 14.0% | 9 | 1 of 3 | 671 |
| 536 | Ritlal Yadav | 13.9% | 5 | 1 of 3 | 595 |
| 400 | Barlow and Chambers execution | 13.9% | 13 | 2 of 7 | 1666 |
| 444 | Abe Saffron | 13.8% | 7 | 4 of 8 | 1509 |
| 730 | Sugarloaf massacre | 13.8% | 5 | 0 of 4 | 941 |
| 1065 | Highwaymen Motorcycle Club | 13.8% | 4 | 0 of 5 | 768 |
| 231 | Ciro Terranova | 13.7% | 8 | 0 of 5 | 910 |
| 688 | Médecins Sans Frontières | 13.7% | 5 | 0 of 7 | 1144 |
| 343 | Frank Ford (Australian politician) | 13.6% | 6 | 0 of 5 | 769 |
| 471 | Murder of Anita Cobby | 13.6% | 8 | 2 of 5 | 1156 |
| 532 | Brahmeshwar Singh | 13.5% | 5 | 0 of 5 | 685 |
| 416 | Alan Bond | 13.5% | 4 | 1 of 8 | 914 |
| 467 | Ronald Ryan | 13.4% | 9 | 2 of 8 | 1476 |
| 1088 | Satan's Choice Motorcycle Club | 13.3% | 6 | 0 of 5 | 734 |
| 798 | Yalova Peninsula massacres | 13.3% | 9 | 0 of 6 | 1149 |
| 698 | Zabiullah Mujahid | 13.3% | 5 | 0 of 5 | 751 |
| 375 | New Zealand | 13.2% | 10 | 4 of 6 | 1305 |
| 556 | Polverino clan | 13.2% | 7 | 1 of 5 | 931 |
| 425 | David Parker (Australian politician) | 13.0% | 6 | 0 of 5 | 792 |
| 1114 | 2022 Soweto shooting | 12.9% | 5 | 0 of 4 | 546 |
| 401 | Malaysia | 12.9% | 10 | 2 of 6 | 1542 |
| 565 | Nuvoletta clan | 12.7% | 6 | 1 of 6 | 1057 |
| 732 | Gnadenhutten massacre | 12.7% | 8 | 1 of 5 | 1356 |
| 772 | Battle of Washita River | 12.6% | 8 | 0 of 5 | 1521 |
| 523 | Jagdish Mahto | 12.6% | 4 | 1 of 6 | 751 |
| 561 | Francesco Schiavone | 12.6% | 7 | 1 of 5 | 951 |
| 1045 | 2021 Nagaland killings | 12.5% | 8 | 1 of 7 | 1133 |
| 983 | Brown's Chicken massacre | 12.5% | 4 | 0 of 4 | 767 |
| 374 | Brothers Hospitallers of Saint John of God | 12.4% | 6 | 0 of 6 | 1045 |
| 998 | Qana massacre | 12.4% | 7 | 1 of 5 | 820 |
| 342 | Derryn Hinch | 12.4% | 8 | 0 of 6 | 1218 |
| 996 | Srebrenica massacre | 12.3% | 3 | 0 of 5 | 802 |
| 1119 | 2016–17 targeted killings in Punjab, India | 12.2% | 3 | 0 of 3 | 586 |
| 892 | Hill 303 massacre | 12.2% | 9 | 0 of 5 | 1121 |
| 518 | Nirav Modi | 12.2% | 8 | 0 of 7 | 1336 |
| 816 | Les Cayes massacre | 12.2% | 7 | 1 of 5 | 917 |
| 345 | Michael Cobb | 12.0% | 4 | 1 of 5 | 677 |
| 586 | Polizia di Stato | 11.9% | 7 | 2 of 6 | 960 |
| 404 | Kath Pettingill | 11.9% | 7 | 1 of 5 | 829 |
| 350 | Frank Smith (New South Wales politician) | 11.7% | 6 | 2 of 6 | 898 |
| 473 | Raymond Edmunds | 11.6% | 4 | 0 of 4 | 583 |
| 568 | Cannes | 11.5% | 4 | 4 of 7 | 1064 |
| 780 | Lattimer massacre | 11.4% | 5 | 0 of 5 | 1032 |
| 1074 | No Surrender Motorcycle Club | 11.3% | 3 | 0 of 4 | 563 |
| 624 | February 2010 Kabul attack | 11.2% | 6 | 4 of 5 | 739 |
| 746 | Piet Retief Delegation massacre | 11.2% | 5 | 3 of 4 | 865 |
| 320 | Soldiers of Odin | 11.1% | 2 | 0 of 4 | 885 |
| 335 | Nomads Motorcycle Club (Australia) | 11.1% | 10 | 1 of 7 | 1355 |
| 1068 | Kings Crew Motorcycle Club | 11.1% | 5 | 1 of 4 | 860 |
| 535 | Lalu Prasad Yadav | 11.0% | 11 | 1 of 9 | 1538 |
| 814 | 1929 Hebron massacre | 10.9% | 6 | 3 of 5 | 1339 |
| 909 | Bình Hòa massacre | 10.9% | 3 | 0 of 4 | 718 |
| 1093 | Tribesmen Motorcycle Club | 10.9% | 4 | 0 of 6 | 628 |
| 717 | Portadown massacre | 10.8% | 4 | 1 of 5 | 911 |
| 997 | Dunblane massacre | 10.8% | 2 | 1 of 4 | 866 |
| 552 | Giuseppe Graviano | 10.8% | 12 | 0 of 6 | 1567 |
| 1021 | 2010 San Fernando massacre | 10.7% | 3 | 3 of 5 | 679 |
| 344 | Bob Woods (politician) | 10.7% | 6 | 0 of 5 | 727 |
| 667 | July 2020 Afghanistan attacks | 10.6% | 5 | 1 of 5 | 826 |
| 466 | Anthony Perish | 10.6% | 4 | 0 of 6 | 892 |
| 584 | Salvatore Lo Piccolo | 10.6% | 5 | 1 of 6 | 1138 |
| 793 | Pinsk massacre | 10.5% | 6 | 0 of 6 | 1117 |
| 490 | Caroline Grills | 10.5% | 3 | 0 of 6 | 682 |
| 567 | Antonino Giuffrè | 10.5% | 3 | 1 of 6 | 796 |
| 754 | Cao Cao's invasion of Xu Province | 10.5% | 2 | 0 of 4 | 845 |
| 664 | Kabul gurdwara attack | 10.5% | 5 | 2 of 4 | 769 |
| 604 | Rome | 10.4% | 5 | 2 of 5 | 1263 |
| 242 | Tongan Crip Gang | 10.3% | 2 | 1 of 3 | 459 |
| 729 | Battle of Waxhaws | 10.3% | 7 | 1 of 5 | 1442 |
| 414 | HIH Insurance | 10.3% | 3 | 2 of 5 | 1125 |
| 689 | Taliban | 10.1% | 7 | 0 of 8 | 1356 |
| 1002 | Laxmanpur Bathe massacre | 10.1% | 3 | 1 of 4 | 657 |
| 911 | Massacre at Huế | 10.0% | 2 | 0 of 4 | 804 |
| 254 | Peckerwood | 10.0% | 5 | 0 of 4 | 888 |
| 384 | Matthew Norman | 10.0% | 6 | 0 of 8 | 1029 |
| 621 | 2009 bombing of Indian embassy in Kabul | 9.9% | 4 | 4 of 5 | 619 |
| 1140 | Jussie Smollett hate crime hoax | 9.9% | 5 | 2 of 8 | 1611 |
| 511 | Mark "Chopper" Read | 9.9% | 6 | 1 of 7 | 1452 |
| 455 | Ned Kelly | 9.9% | 6 | 5 of 8 | 1526 |
| 426 | Deputy Premier of Western Australia | 9.8% | 5 | 1 of 3 | 594 |
| 733 | Olowalu Massacre | 9.7% | 3 | 0 of 4 | 900 |
| 492 | Matthew James Harris | 9.7% | 7 | 0 of 6 | 881 |
| 516 | Vijay Mallya | 9.6% | 3 | 2 of 6 | 899 |
| 527 | Jagannath Mishra | 9.5% | 2 | 0 of 7 | 1149 |
| 397 | Tan Duc Thanh Nguyen | 9.5% | 3 | 0 of 7 | 1036 |
| 1046 | Shedden massacre | 9.5% | 2 | 2 of 5 | 1026 |
| 614 | Barcelona | 9.5% | 6 | 0 of 7 | 1522 |
| 519 | Natwarlal | 9.4% | 3 | 1 of 5 | 770 |
| 723 | 1740 Batavia massacre | 9.4% | 6 | 3 of 4 | 1303 |
| 521 | G. Janardhana Reddy | 9.4% | 1 | 1 of 7 | 739 |
| 548 | Antonio Imerti | 9.4% | 1 | 1 of 3 | 643 |
| 1073 | Nomads Motorcycle Club (Australia) | 9.4% | 1 | 0 of 5 | 623 |
| 368 | Robert 'Dolly' Dunn | 9.4% | 2 | 0 of 3 | 570 |
| 317 | Romanian mafia | 9.1% | 3 | 1 of 6 | 1428 |
| 1017 | Virginia Tech shooting | 9.1% | 0 | 2 of 5 | 786 |
| 797 | Centralia massacre (Washington) | 9.1% | 7 | 2 of 6 | 1682 |
| 908 | Binh Tai Massacre | 9.0% | 4 | 0 of 4 | 701 |
| 769 | Centralia Massacre (Missouri) | 9.0% | 6 | 1 of 4 | 1005 |
| 952 | 1983 Lucanamarca massacre | 9.0% | 3 | 0 of 3 | 729 |
| 337 | Rebels Motorcycle Club | 8.9% | 2 | 1 of 5 | 932 |
| 522 | Abdul Karim Telgi | 8.9% | 3 | 1 of 7 | 1027 |
| 422 | Theresa Lawson | 8.9% | 4 | 0 of 4 | 749 |
| 766 | Bear River Massacre | 8.8% | 3 | 0 of 4 | 755 |
| 651 | Kabul ambulance bombing | 8.8% | 1 | 2 of 3 | 460 |
| 301 | Organised crime in Pakistan | 8.8% | 6 | 1 of 6 | 1201 |
| 1022 | 2011 Norway attacks | 8.8% | 4 | 0 of 5 | 836 |
| 440 | Lenny McPherson | 8.7% | 3 | 0 of 7 | 1012 |
| 380 | Peter Scully | 8.7% | 4 | 0 of 5 | 890 |
| 410 | Lewis Moran | 8.6% | 7 | 1 of 6 | 1300 |
| 283 | Jackson Street Boys | 8.6% | 2 | 1 of 3 | 583 |
| 229 | Frank Scalice | 8.6% | 2 | 0 of 6 | 863 |
| 515 | Rajat Gupta | 8.6% | 3 | 1 of 6 | 941 |
| 990 | Cave of the Patriarchs massacre | 8.5% | 5 | 1 of 5 | 895 |
| 563 | Erminia Giuliano | 8.4% | 1 | 1 of 5 | 538 |
| 448 | James Beauregard-Smith | 8.4% | 4 | 0 of 4 | 696 |
| 365 | Gregory David Roberts | 8.3% | 4 | 1 of 5 | 869 |
| 1006 | Columbine High School massacre | 8.3% | 3 | 1 of 4 | 704 |
| 898 | Lari massacre | 8.3% | 4 | 0 of 4 | 645 |
| 863 | Oradour-sur-Glane massacre | 8.3% | 3 | 1 of 5 | 986 |
| 896 | Sancheong–Hamyang massacre | 8.3% | 3 | 0 of 4 | 598 |
| 491 | Paul Steven Haigh | 8.2% | 3 | 0 of 3 | 496 |
| 559 | Mario Fabbrocino | 8.2% | 3 | 0 of 4 | 594 |
| 366 | Shantaram (novel) | 8.2% | 2 | 2 of 6 | 1060 |
| 901 | Mueda | 8.1% | 2 | 0 of 4 | 619 |
| 364 | Victor Peirce | 8.1% | 5 | 2 of 6 | 953 |
| 782 | 1900 Amur anti-Chinese pogroms | 8.0% | 2 | 1 of 4 | 833 |
| 250 | Menace of Destruction | 7.9% | 3 | 0 of 4 | 959 |
| 499 | John and Sarah Makin | 7.8% | 4 | 1 of 6 | 1244 |
| 293 | 18th Street gang | 7.8% | 2 | 2 of 5 | 956 |
| 690 | Aqcha District | 7.7% | 2 | 0 of 3 | 512 |
| 500 | Backpacker murders | 7.7% | 4 | 0 of 7 | 1467 |
| 606 | Salvatore Russo | 7.5% | 2 | 0 of 4 | 486 |
| 656 | 2019 Kabul mosque bombing | 7.4% | 3 | 2 of 4 | 680 |
| 681 | Kandahar | 7.4% | 2 | 0 of 6 | 1100 |
| 931 | Tlatelolco massacre | 7.3% | 4 | 3 of 4 | 1279 |
| 555 | Giorgio De Stefano (1948) | 7.3% | 1 | 1 of 4 | 895 |
| 792 | Vyborg massacre | 7.3% | 4 | 2 of 6 | 1342 |
| 1016 | 2006 Qana airstrike | 7.2% | 1 | 2 of 5 | 791 |
| 611 | Gaetano Fidanzati | 7.2% | 3 | 0 of 6 | 1289 |
| 520 | Ramalinga Raju | 7.2% | 1 | 0 of 6 | 755 |
| 794 | Jallianwala Bagh massacre | 7.2% | 3 | 1 of 6 | 1229 |
| 672 | 2020 Kabul University attack | 7.2% | 6 | 3 of 5 | 1119 |
| 620 | 2009 NATO Afghanistan headquarters bombing | 7.1% | 1 | 1 of 4 | 455 |
| 610 | Calatafimi-Segesta | 6.9% | 4 | 2 of 6 | 1929 |
| 740 | Navarino massacre | 6.8% | 5 | 0 of 5 | 985 |
| 791 | Porvenir massacre (1918) | 6.8% | 4 | 1 of 7 | 1481 |
| 383 | Michael Czugaj | 6.8% | 4 | 0 of 6 | 980 |
| 454 | Maddison Hall | 6.8% | 1 | 1 of 4 | 671 |
| 506 | Arnold Sodeman | 6.7% | 5 | 2 of 7 | 1247 |
| 1087 | Sadistic Souls Motorcycle Club | 6.7% | 2 | 0 of 5 | 679 |
| 389 | Michael McAuliffe (drug trafficker) | 6.7% | 0 | 0 of 5 | 724 |
| 631 | 2013 Afghan presidential palace attack | 6.6% | 2 | 0 of 4 | 352 |
| 546 | Umberto Bellocco | 6.6% | 2 | 0 of 4 | 764 |
| 531 | Anand Mohan Singh | 6.5% | 2 | 0 of 5 | 787 |
| 720 | Zhang Xianzhong | 6.5% | 5 | 1 of 7 | 1494 |
| 591 | Caracas | 6.5% | 2 | 1 of 5 | 1311 |
| 799 | Gando massacre | 6.5% | 3 | 3 of 4 | 1005 |
| 504 | Hydrochloric acid | 6.5% | 5 | 4 of 6 | 1268 |
| 678 | 2009 bombing of Indian embassy in Kabul | 6.5% | 3 | 5 of 5 | 748 |
| 702 | 1033 Fez massacre | 6.5% | 2 | 0 of 4 | 826 |
| 514 | Rajkissore Dutt | 6.4% | 5 | 0 of 5 | 863 |
| 316 | Pettingill family | 6.4% | 3 | 0 of 5 | 910 |
| 558 | Girolamo Molè | 6.3% | 2 | 0 of 5 | 667 |
| 864 | Distomo massacre | 6.3% | 1 | 1 of 5 | 845 |
| 707 | Crow Creek massacre | 6.3% | 2 | 1 of 5 | 1357 |
| 339 | John Curtin | 6.2% | 1 | 0 of 8 | 1477 |
| 238 | Grape Street Watts Crips | 6.2% | 3 | 0 of 5 | 877 |
| 703 | Siege of Jerusalem (1099) | 6.1% | 3 | 0 of 5 | 1396 |
| 318 | Russian mafia | 6.1% | 1 | 0 of 5 | 1041 |
| 554 | Giovanni Brusca | 6.0% | 2 | 0 of 7 | 1298 |
| 538 | Renato Cinquegranella | 6.0% | 2 | 0 of 3 | 587 |
| 982 | Carandiru massacre | 6.0% | 3 | 0 of 5 | 873 |
| 268 | Puente 13 | 6.0% | 2 | 0 of 4 | 591 |
| 282 | Black Dragons (gang) | 6.0% | 2 | 0 of 4 | 675 |
| 706 | Sicilian Vespers | 5.9% | 4 | 1 of 6 | 1108 |
| 895 | Ganghwa massacre | 5.9% | 2 | 0 of 4 | 672 |
| 294 | Abergil crime family | 5.8% | 1 | 1 of 6 | 1034 |
| 987 | Başbağlar massacre | 5.8% | 2 | 0 of 4 | 579 |
| 865 | Wola massacre | 5.7% | 1 | 1 of 4 | 783 |
| 753 | Roman conquest of Anglesey | 5.6% | 3 | 1 of 6 | 1321 |
| 1020 | Maguindanao massacre | 5.6% | 0 | 2 of 5 | 774 |
| 640 | 2015 Spanish Embassy attack in Kabul | 5.6% | 2 | 0 of 4 | 545 |
| 661 | 2 and 5 September 2019 Kabul bombings | 5.5% | 1 | 0 of 4 | 657 |
| 675 | Fall of Kabul (2021) | 5.5% | 3 | 0 of 6 | 1336 |
| 995 | 1995 Kohima Massacre | 5.5% | 1 | 1 of 3 | 573 |
| 662 | 17 September 2019 Afghanistan bombings | 5.4% | 1 | 1 of 4 | 504 |
| 774 | Rock Springs massacre | 5.4% | 0 | 1 of 4 | 1007 |
| 991 | Shell House massacre | 5.3% | 3 | 0 of 5 | 828 |
| 984 | Ethnic cleansing of Georgians in Sukhumi | 5.3% | 1 | 0 of 4 | 622 |
| 612 | Milan | 5.3% | 3 | 3 of 7 | 2202 |
| 623 | January 2010 Kabul attack | 5.3% | 1 | 0 of 4 | 400 |
| 727 | Affair at Little Egg Harbor | 5.3% | 2 | 2 of 3 | 968 |
| 637 | 7 August 2015 Kabul attacks | 5.2% | 2 | 0 of 4 | 678 |
| 767 | Lawrence Massacre | 5.2% | 0 | 1 of 6 | 1545 |
| 659 | 7 August 2019 Kabul bombing | 5.1% | 1 | 2 of 4 | 864 |
| 1131 | Stabbing of Salman Rushdie | 5.1% | 1 | 3 of 6 | 1063 |
| 539 | Matteo Messina Denaro | 5.1% | 3 | 1 of 6 | 1499 |
| 666 | June 2020 Afghanistan attacks | 5.1% | 1 | 0 of 5 | 712 |
| 632 | January 2014 Kabul restaurant attack | 4.9% | 1 | 1 of 4 | 310 |
| 687 | Dashte Barchi | 4.9% | 1 | 1 of 4 | 860 |
| 605 | Mignano Monte Lungo | 4.8% | 0 | 2 of 4 | 595 |
| 300 | Playboys (gang) | 4.7% | 1 | 0 of 5 | 872 |
| 795 | Menemen massacre | 4.7% | 1 | 1 of 4 | 937 |
| 705 | Massacre at Béziers | 4.7% | 2 | 0 of 4 | 1435 |
| 894 | Sinchon Massacre | 4.7% | 1 | 0 of 5 | 1092 |
| 985 | Waco siege | 4.7% | 2 | 1 of 5 | 971 |
| 642 | Kabul attack on Canadian Embassy guards | 4.6% | 0 | 5 of 5 | 421 |
| 755 | Erfurt massacre (1349) | 4.5% | 2 | 1 of 5 | 752 |
| 302 | Sam Gor | 4.5% | 2 | 0 of 5 | 890 |
| 776 | Wounded Knee Massacre | 4.5% | 3 | 1 of 5 | 1252 |
| 245 | Galloping Goose Motorcycle Club | 4.5% | 1 | 0 of 4 | 674 |
| 264 | El Monte Flores | 4.5% | 1 | 0 of 5 | 719 |
| 306 | Antipodean Resistance | 4.4% | 2 | 0 of 6 | 950 |
| 262 | Azusa 13 | 4.4% | 0 | 1 of 5 | 568 |
| 249 | Los Angeles crime family | 4.3% | 1 | 0 of 6 | 1332 |
| 603 | Reggio Calabria | 4.3% | 1 | 1 of 6 | 1006 |
| 537 | Attilio Cubeddu | 4.2% | 0 | 0 of 4 | 769 |
| 1069 | Lone Legion Brotherhood | 4.2% | 1 | 0 of 4 | 559 |
| 644 | American University of Afghanistan attack | 4.2% | 0 | 3 of 3 | 559 |
| 585 | Daniele Emmanuello | 4.1% | 1 | 0 of 3 | 417 |
| 790 | Abschwangen massacre | 4.1% | 2 | 2 of 5 | 884 |
| 581 | Bernardo Provenzano | 4.0% | 0 | 0 of 6 | 880 |
| 613 | Sardinia | 3.9% | 1 | 2 of 4 | 882 |
| 674 | 2021 Kabul school bombing | 3.9% | 2 | 3 of 5 | 758 |
| 654 | 30 April 2018 Kabul suicide bombings | 3.8% | 1 | 0 of 4 | 690 |
| 704 | Massacre of the Latins | 3.8% | 1 | 0 of 6 | 1065 |
| 1018 | Mardin engagement ceremony massacre | 3.8% | 1 | 0 of 5 | 619 |
| 450 | Port Arthur massacre (Australia) | 3.7% | 2 | 3 of 7 | 1556 |
| 597 | Casal di Principe | 3.7% | 2 | 0 of 4 | 646 |
| 579 | Paolo Di Lauro | 3.7% | 1 | 0 of 5 | 771 |
| 583 | Giuseppe Bellocco | 3.6% | 1 | 0 of 5 | 864 |
| 771 | Sand Creek massacre | 3.5% | 1 | 1 of 5 | 1117 |
| 893 | Goyang Geumjeong Cave massacre | 3.5% | 2 | 0 of 4 | 724 |
| 676 | 2021 Kabul hospital attack | 3.5% | 0 | 3 of 5 | 810 |
| 638 | 10 August 2015 Kabul suicide bombing | 3.3% | 1 | 0 of 4 | 521 |
| 1117 | Sábado de mierda | 3.3% | 1 | 0 of 4 | 707 |
| 540 | Giovanni Motisi | 3.2% | 2 | 0 of 5 | 1026 |
| 660 | 17 August 2019 Kabul bombing | 3.2% | 1 | 2 of 4 | 780 |
| 1015 | Mahmudiyah rape and killings | 3.2% | 1 | 1 of 5 | 848 |
| 357 | Richard Face | 3.2% | 1 | 0 of 4 | 510 |
| 736 | Battle of Praga | 3.1% | 2 | 2 of 5 | 1150 |
| 643 | July 2016 Kabul bombing | 3.0% | 1 | 3 of 4 | 969 |
| 524 | Abhay Kushwaha | 2.9% | 1 | 1 of 5 | 759 |
| 582 | Corleone | 2.8% | 1 | 0 of 6 | 899 |
| 986 | Sivas massacre | 2.8% | 1 | 0 of 4 | 757 |
| 636 | 2015 Kabul Parliament attack | 2.8% | 0 | 3 of 4 | 655 |
| 628 | 2011 Afghanistan Ashura bombings | 2.7% | 0 | 4 of 5 | 481 |
| 553 | Leoluca Bagarella | 2.7% | 1 | 2 of 5 | 1124 |
| 526 | Pradeep Mahto | 2.6% | 1 | 0 of 4 | 583 |
| 635 | 2015 Park Palace guesthouse attack | 2.6% | 0 | 3 of 4 | 547 |
| 711 | Ottoman–Venetian War (1570–1573) | 2.5% | 2 | 0 of 5 | 1183 |
| 600 | Polistena | 2.5% | 1 | 1 of 5 | 984 |
| 747 | Weenen massacre | 2.5% | 2 | 2 of 5 | 927 |
| 657 | 1 July 2019 Kabul attack | 2.5% | 0 | 4 of 5 | 734 |
| 566 | Maria Licciardi | 2.1% | 1 | 0 of 7 | 949 |
| 551 | Catania | 2.0% | 1 | 0 of 7 | 1624 |
| 29 | Kirkwood City Council shooting | 1.8% | 1 | 0 of 7 | 2377 |
| 59 | Charles Starkweather | 1.8% | 2 | 0 of 9 | 2369 |
| 207 | Peter Gotti | 1.5% | 2 | 0 of 7 | 1864 |
| 82 | Abu Omar al-Shishani | 1.5% | 1 | 0 of 6 | 1920 |
| 296 | Bloods | 1.5% | 1 | 0 of 6 | 954 |
| 411 | Mark Moran (criminal) | 1.5% | 1 | 0 of 4 | 757 |
| 91 | Ahmad Abousamra | 1.4% | 1 | 0 of 8 | 2248 |
| 739 | Madulla | 1.4% | 1 | 0 of 4 | 711 |
| 38 | Sutherland Springs church shooting | 1.4% | 1 | 0 of 8 | 1895 |
| 303 | Snakehead (gang) | 1.2% | 1 | 0 of 6 | 855 |
| 87 | Abu Mohammad al-Adnani | 1.2% | 1 | 0 of 9 | 2319 |
| 197 | Sonny Franzese | 1.1% | 1 | 0 of 9 | 2044 |
| 14 | 2016 Kalamazoo shootings | 1.1% | 1 | 0 of 6 | 1988 |
| 204 | Gene Gotti | 1.1% | 1 | 1 of 6 | 1384 |
| 57 | Howard Unruh | 1.1% | 1 | 0 of 8 | 2592 |
| 609 | Luigi Esposito | 1.1% | 1 | 0 of 6 | 1302 |
| 11 | 2011 Seal Beach shooting | 1.1% | 1 | 1 of 9 | 2201 |
| 214 | Tommy Lucchese | 1.0% | 1 | 1 of 10 | 2513 |
| 50 | Jim Jumper massacre | 0.9% | 1 | 0 of 8 | 1705 |
| 219 | Joseph Massino | 0.9% | 1 | 0 of 10 | 2798 |
| 156 | Tony Accardo | 0.8% | 1 | 0 of 8 | 1957 |
| 222 | Carmine Persico | 0.8% | 1 | 0 of 8 | 2346 |
| 16 | 2019 Dayton shooting | 0.7% | 1 | 0 of 5 | 1654 |
| 608 | Sperone | 0.7% | 1 | 1 of 5 | 992 |
| 213 | Lucky Luciano | 0.5% | 1 | 0 of 7 | 2188 |
| 224 | Philip Rastelli | 0.5% | 1 | 0 of 9 | 2228 |
| 106 | Abu Muhammad al-Shimali | 0.5% | 1 | 0 of 7 | 1983 |

## Posts that pass

3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 30, 31, 32, 33, 34, 35, 36, 37, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 51, 52, 53, 54, 55, 56, 58, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 83, 84, 85, 86, 88, 89, 90, 92, 93, 94, 95, 96, 97, 98, 99, 100, 102, 103, 104, 105, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 119, 120, 121, 122, 123, 125, 126, 127, 128, 129, 130, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 198, 199, 200, 201, 202, 203, 205, 206, 208, 209, 210, 211, 212, 215, 216, 217, 218, 220, 221, 223, 225, 226, 227, 228, 263, 299, 382, 394, 525, 528, 529, 530, 533, 534, 543, 544, 569, 578, 580, 598, 615, 627, 629, 630, 633, 634, 639, 645, 649, 650, 652, 653, 655, 658, 663, 668, 669, 670, 671, 673, 679, 680, 697, 715, 719, 721, 862, 946, 948, 1043, 1054, 1057, 1070, 1071, 1085, 1091, 1094, 1104
