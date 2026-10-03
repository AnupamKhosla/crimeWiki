# Posts that need a rewrite

Generated 3 October 2026 from the live snapshot `tmp/live-snapshot/posts-20261003.tsv.gz`
(1,121 live posts; posts 1 and 2 are blog templates, not articles, and are left out).
Rebuild with `python3 tmp/live-rewrite/make_list.py`. Status comes from
`tmp/live-rewrite/articles/` and `tmp/live-rewrite/blocked/`.

Standard: 1,200 to 1,500 words of real content, sources the writer actually opened.
A "homepage-only" source is a link to a site front page (for example `https://www.bbc.com/`),
which proves nothing and counts as a missing source.

| Tier | Meaning | Posts | Of which have only homepage-only or no sources |
|---|---|---|---|
| 1 | Under 1,000 words. Rewrite first, shortest first. | 532 | 25 |
| 2 | 1,000 to 1,199 words. Lengthen to 1,200 or more. | 144 | 3 |
| 3 | 1,200 words or more, but no usable source links. Keep the text, fix the sources and facts. | 15 | 15 |

Posts not listed (1,200+ words with at least one real source link) have not been fact-checked either.

## Tier 1: under 1,000 words

| Post | Title | Category | Words | Sources (homepage-only / total) | Status |
|---|---|---|---|---|---|
| 632 | January 2014 Kabul restaurant attack | Crimes | 299 | 0 / 4 | drafted, 1444 words |
| 890 | Seoul National University Hospital massacre | Crimes | 300 | 4 / 4 | drafted, 1698 words |
| 649 | 28 December 2017 Kabul suicide bombing | Crimes | 333 | 0 / 4 | drafted, 1426 words |
| 631 | 2013 Afghan presidential palace attack | Crimes | 335 | 0 / 4 | drafted, 1310 words |
| 653 | 22 April 2018 Kabul suicide bombing | Crimes | 359 | 0 / 4 | drafted, 1458 words |
| 1071 | Mobshitters | Groups | 359 | 4 / 4 | blocked (too thin) |
| 533 | Shankar Singh (politician) | Criminals | 367 | 4 / 4 | drafted, 1063 words UNDER 1,200 |
| 528 | Dadan Pahalwan | Criminals | 371 | 3 / 4 | pending |
| 652 | March 2018 Kabul suicide bombing | Crimes | 377 | 0 / 4 | drafted, 1328 words |
| 1135 | Vikas Thakur | Blog | 383 | 1 / 4 | pending |
| 1058 | Gremium Motorcycle Club | Groups | 384 | 3 / 4 | pending |
| 623 | January 2010 Kabul attack | Crimes | 387 | 0 / 4 | pending |
| 426 | Deputy Premier of Western Australia | Criminals | 389 | 3 / 4 | pending |
| 1076 | Notorious Motorcycle Club (Germany) | Groups | 389 | 2 / 4 | pending |
| 888 | Mungyeong massacre | Crimes | 390 | 3 / 4 | pending |
| 1085 | Road Runners Motorcycle Club | Groups | 396 | 4 / 4 | pending |
| 585 | Daniele Emmanuello | Criminals | 402 | 4 / 4 | pending |
| 1054 | Diablos Motorcycle Club | Groups | 402 | 4 / 4 | pending |
| 276 | Vineland Boys | Groups | 408 | 2 / 4 | pending |
| 642 | Kabul attack on Canadian Embassy guards | Crimes | 410 | 0 / 4 | pending |
| 235 | Chosen Few Motorcycle Club | Groups | 412 | 0 / 4 | pending |
| 326 | Club Deroes | Groups | 412 | 2 / 4 | pending |
| 1059 | Grim Reapers Motorcycle Club (Canada) | Groups | 412 | 0 / 5 | pending |
| 1083 | Road Knights | Groups | 415 | 0 / 4 | pending |
| 639 | 22 August 2015 Kabul suicide bombing | Crimes | 419 | 0 / 4 | pending |
| 1055 | El Forastero Motorcycle Club | Groups | 430 | 2 / 4 | pending |
| 650 | 2018 Inter-Continental Hotel Kabul attack | Crimes | 432 | 0 / 4 | pending |
| 1038 | Sobane Da massacre | Crimes | 444 | 0 / 5 | pending |
| 242 | Tongan Crip Gang | Groups | 446 | 0 / 4 | pending |
| 651 | Kabul ambulance bombing | Crimes | 451 | 0 / 5 | pending |
| 1057 | Free Souls Motorcycle Club | Groups | 454 | 4 / 4 | pending |
| 1064 | Highway 61 Motorcycle Club | Groups | 455 | 1 / 5 | pending |
| 628 | 2011 Afghanistan Ashura bombings | Crimes | 458 | 0 / 4 | pending |
| 645 | September 2016 Kabul attacks | Crimes | 461 | 0 / 4 | pending |
| 928 | Namyangju massacre | Crimes | 462 | 0 / 4 | pending |
| 1091 | Solo Angeles | Groups | 463 | 1 / 4 | pending |
| 1095 | Vendettas Motorcycle Club | Groups | 468 | 2 / 4 | pending |
| 606 | Salvatore Russo | Criminals | 469 | 0 / 4 | pending |
| 491 | Paul Steven Haigh | Criminals | 471 | 0 / 4 | pending |
| 976 | Bara massacre | Crimes | 476 | 0 / 4 | pending |
| 271 | Shelltown, San Diego | Groups | 479 | 4 / 4 | pending |
| 241 | Sons of Samoa | Groups | 481 | 0 / 5 | pending |
| 690 | Aqcha District | Criminals | 481 | 0 / 4 | pending |
| 658 | 28 July 2019 Kabul suicide bombing | Crimes | 482 | 0 / 4 | pending |
| 263 | Culver City Boys | Groups | 483 | 0 / 5 | pending |
| 362 | Darcy Dugan | Criminals | 483 | 5 / 5 | pending |
| 357 | Richard Face | Criminals | 485 | 1 / 4 | pending |
| 329 | Finks Motorcycle Club | Groups | 493 | 2 / 4 | pending |
| 939 | Letipea massacre | Crimes | 494 | 3 / 4 | pending |
| 662 | 17 September 2019 Afghanistan bombings | Crimes | 495 | 0 / 5 | pending |
| 764 | Gallinas massacre | Crimes | 496 | 1 / 4 | pending |
| 332 | Highway 61 Motorcycle Club | Groups | 498 | 3 / 4 | pending |
| 620 | 2009 NATO Afghanistan headquarters bombing | Crimes | 499 | 0 / 5 | pending |
| 327 | Coffin Cheaters | Groups | 503 | 4 / 4 | pending |
| 461 | Francesco Mangione | Criminals | 507 | 0 / 4 | pending |
| 638 | 10 August 2015 Kabul suicide bombing | Crimes | 507 | 0 / 4 | pending |
| 275 | White Fence | Groups | 512 | 2 / 4 | pending |
| 979 | Boipatong massacre | Crimes | 514 | 0 / 4 | pending |
| 1097 | Warlocks Motorcycle Club (Florida) | Groups | 515 | 3 / 4 | pending |
| 1005 | Blue Market massacre | Crimes | 517 | 0 / 5 | pending |
| 436 | Alphonse Gangitano | Criminals | 518 | 0 / 4 | pending |
| 640 | 2015 Spanish Embassy attack in Kabul | Crimes | 521 | 0 / 5 | pending |
| 1077 | Original Red Devils Motorcycle Club | Groups | 521 | 1 / 4 | pending |
| 619 | February 2009 raids on Kabul | Crimes | 523 | 0 / 4 | pending |
| 526 | Pradeep Mahto | Criminals | 526 | 5 / 5 | pending |
| 1069 | Lone Legion Brotherhood | Groups | 526 | 0 / 4 | pending |
| 1096 | Warlocks Motorcycle Club (Pennsylvania) | Groups | 527 | 2 / 4 | pending |
| 512 | Amit Bhardwaj | Criminals | 528 | 0 / 5 | pending |
| 646 | January 2017 Afghanistan bombings | Crimes | 528 | 0 / 4 | pending |
| 970 | 1990 Batticaloa massacre | Crimes | 528 | 0 / 4 | pending |
| 1114 | 2022 Soweto shooting | Crimes | 528 | 0 / 4 | pending |
| 679 | 10 August 2015 Kabul suicide bombing | Crimes | 534 | 0 / 4 | pending |
| 596 | Raffaele Diana | Criminals | 536 | 0 / 4 | pending |
| 993 | 1994 Mokokchung Massacre | Crimes | 537 | 0 / 4 | pending |
| 607 | Pasquale Russo | Criminals | 539 | 0 / 4 | pending |
| 634 | December 2014 Kabul bombings | Crimes | 541 | 0 / 5 | pending |
| 1074 | No Surrender Motorcycle Club | Groups | 541 | 1 / 5 | pending |
| 635 | 2015 Park Palace guesthouse attack | Crimes | 543 | 0 / 5 | pending |
| 669 | September 2020 Afghanistan attacks | Crimes | 544 | 0 / 5 | pending |
| 530 | Jagdish Sharma | Criminals | 545 | 2 / 4 | pending |
| 1080 | Pissed Off Bastards of Bloomington | Groups | 545 | 2 / 4 | pending |
| 256 | Public Enemy No. 1 (gang) | Groups | 548 | 0 / 4 | pending |
| 925 | Maratha, Santalaris and Aloda massacre | Crimes | 549 | 0 / 4 | pending |
| 269 | Santa Monica 13 | Groups | 550 | 2 / 4 | pending |
| 1129 | Shivaji Surathkal | Blog | 550 | 0 / 5 | pending |
| 262 | Azusa 13 | Groups | 551 | 0 / 5 | pending |
| 477 | Gregory Brazel | Criminals | 551 | 0 / 4 | pending |
| 917 | Ketnar Bil massacre | Crimes | 551 | 0 / 4 | pending |
| 1043 | Wehda Street airstrikes | Crimes | 551 | 0 / 5 | pending |
| 995 | 1995 Kohima Massacre | Crimes | 552 | 0 / 5 | pending |
| 368 | Robert 'Dolly' Dunn | Criminals | 553 | 0 / 4 | pending |
| 1037 | Ogossagou massacre | Crimes | 555 | 0 / 5 | pending |
| 1070 | Lost Breed | Groups | 558 | 0 / 4 | pending |
| 431 | Ray Williams (businessman) | Criminals | 559 | 0 / 4 | pending |
| 563 | Erminia Giuliano | Criminals | 559 | 0 / 5 | pending |
| 1052 | Cannonball Motorcycle Club | Groups | 559 | 0 / 5 | pending |
| 272 | Toonerville Rifa 13 | Groups | 561 | 3 / 4 | pending |
| 1051 | Brother Speed | Groups | 563 | 1 / 5 | pending |
| 473 | Raymond Edmunds | Criminals | 564 | 0 / 5 | pending |
| 351 | Francis Abigail | Criminals | 566 | 0 / 4 | pending |
| 1066 | Iron Horsemen | Groups | 568 | 4 / 4 | pending |
| 680 | 22 August 2015 Kabul suicide bombing | Crimes | 570 | 0 / 4 | pending |
| 855 | Dominopol massacre | Crimes | 570 | 2 / 5 | pending |
| 257 | Satanas (gang) | Groups | 571 | 0 / 4 | pending |
| 283 | Jackson Street Boys | Groups | 571 | 0 / 4 | pending |
| 896 | Sancheong–Hamyang massacre | Crimes | 572 | 1 / 4 | pending |
| 538 | Renato Cinquegranella | Criminals | 574 | 0 / 5 | pending |
| 1067 | Iron Order Motorcycle Club | Groups | 574 | 2 / 4 | pending |
| 1082 | Rebels Motorcycle Club (Canada) | Groups | 574 | 4 / 5 | pending |
| 1099 | Ashfaqulla Khan | Criminals | 575 | 2 / 4 | pending |
| 536 | Ritlal Yadav | Criminals | 576 | 0 / 4 | pending |
| 671 | November 2020 Afghanistan attacks | Crimes | 576 | 0 / 5 | pending |
| 601 | Salvatore Miceli | Criminals | 578 | 0 / 5 | pending |
| 1119 | 2016–17 targeted killings in Punjab, India | Crimes | 578 | 0 / 5 | pending |
| 559 | Mario Fabbrocino | Criminals | 579 | 0 / 5 | pending |
| 605 | Mignano Monte Lungo | Criminals | 579 | 2 / 4 | pending |
| 622 | 2009 UN guest house attack in Kabul | Crimes | 579 | 0 / 4 | pending |
| 987 | Başbağlar massacre | Crimes | 579 | 0 / 4 | pending |
| 268 | Puente 13 | Groups | 580 | 0 / 4 | pending |
| 1120 | Candy (TV series) | Blog | 583 | 0 / 5 | pending |
| 338 | 2021 Lynchings for sacrilege in Punjab | Crimes | 586 | 0 / 5 | pending |
| 1090 | Sin City Deciples Motorcycle Club | Groups | 586 | 0 / 4 | pending |
| 914 | Hà My massacre | Crimes | 587 | 0 / 4 | pending |
| 654 | 30 April 2018 Kabul suicide bombings | Crimes | 588 | 0 / 4 | pending |
| 529 | Mohammad Shahabuddin | Criminals | 590 | 4 / 5 | pending |
| 627 | 2011 attack on the United States embassy, Kabul | Crimes | 590 | 0 / 5 | pending |
| 273 | Varrio Nuevo Estrada | Groups | 591 | 3 / 4 | pending |
| 1053 | Chicanos Motorcycle Club | Groups | 591 | 0 / 5 | pending |
| 644 | American University of Afghanistan attack | Crimes | 592 | 0 / 4 | pending |
| 598 | Marina di Gioiosa Ionica | Criminals | 593 | 2 / 4 | pending |
| 1048 | Black Pistons Motorcycle Club | Groups | 593 | 0 / 4 | pending |
| 443 | Nik Radev | Criminals | 594 | 0 / 4 | pending |
| 901 | Mueda | Crimes | 596 | 0 / 4 | pending |
| 1056 | Finks Motorcycle Club | Groups | 596 | 1 / 5 | pending |
| 1093 | Tribesmen Motorcycle Club | Groups | 596 | 0 / 5 | pending |
| 858 | Kalavryta massacre | Crimes | 597 | 3 / 5 | pending |
| 1060 | Grim Reapers Motorcycle Club (USA) | Groups | 597 | 2 / 4 | pending |
| 1073 | Nomads Motorcycle Club (Australia) | Groups | 598 | 0 / 4 | pending |
| 866 | Sant'Anna di Stazzema massacre | Crimes | 605 | 0 / 5 | pending |
| 655 | September 2018 Kabul attacks | Crimes | 608 | 0 / 5 | pending |
| 377 | Patrick Power (lawyer) | Criminals | 611 | 0 / 5 | pending |
| 1094 | Trust Motorcycle Club | Groups | 611 | 2 / 4 | pending |
| 490 | Caroline Grills | Criminals | 613 | 1 / 4 | pending |
| 663 | 6 March 2020 Kabul shooting | Crimes | 614 | 0 / 5 | pending |
| 291 | Black Guerrilla Family | Groups | 615 | 4 / 5 | pending |
| 1128 | Crime Patrol (TV series) | Blog | 617 | 0 / 5 | pending |
| 1018 | Mardin engagement ceremony massacre | Crimes | 618 | 0 / 4 | pending |
| 1092 | Sons of Silence | Groups | 620 | 2 / 4 | pending |
| 1063 | Hell's Lovers | Groups | 621 | 1 / 5 | pending |
| 841 | Arakan massacres in 1942 | Crimes | 622 | 4 / 4 | pending |
| 1007 | Dolphinarium discotheque massacre | Crimes | 624 | 0 / 5 | pending |
| 597 | Casal di Principe | Criminals | 625 | 2 / 4 | pending |
| 633 | 2014 Kabul Serena Hotel attack | Crimes | 625 | 0 / 5 | pending |
| 984 | Ethnic cleansing of Georgians in Sukhumi | Crimes | 625 | 0 / 4 | pending |
| 548 | Antonio Imerti | Criminals | 630 | 0 / 4 | pending |
| 636 | 2015 Kabul Parliament attack | Crimes | 633 | 0 / 4 | pending |
| 697 | Id Gah Mosque | Criminals | 633 | 0 / 4 | pending |
| 1084 | Road Rats Motorcycle Club | Groups | 637 | 2 / 5 | pending |
| 661 | 2 and 5 September 2019 Kabul bombings | Crimes | 638 | 0 / 4 | pending |
| 906 | Palma Sola massacre | Crimes | 638 | 0 / 4 | pending |
| 435 | George Freeman (bookmaker) | Criminals | 639 | 0 / 4 | pending |
| 884 | Babrra massacre | Crimes | 642 | 0 / 4 | pending |
| 1102 | Sidhu Moose Wala | Blog | 643 | 1 / 4 | pending |
| 454 | Maddison Hall | Criminals | 644 | 0 / 5 | pending |
| 448 | James Beauregard-Smith | Criminals | 646 | 0 / 4 | pending |
| 877 | Haifa Oil Refinery massacre | Crimes | 646 | 0 / 5 | pending |
| 1002 | Laxmanpur Bathe massacre | Crimes | 646 | 0 / 5 | pending |
| 1027 | 2014 Bentiu massacre | Crimes | 646 | 0 / 5 | pending |
| 999 | Ghulja incident | Crimes | 648 | 0 / 5 | pending |
| 750 | Killough massacre | Crimes | 650 | 1 / 4 | pending |
| 850 | Dzyatlava massacre | Crimes | 650 | 3 / 5 | pending |
| 621 | 2009 bombing of Indian embassy in Kabul | Crimes | 651 | 0 / 5 | pending |
| 532 | Brahmeshwar Singh | Criminals | 655 | 0 / 5 | pending |
| 245 | Galloping Goose Motorcycle Club | Groups | 656 | 4 / 4 | pending |
| 1125 | Violent Crime Control and Law Enforcement Act | Blog | 657 | 0 / 5 | pending |
| 637 | 7 August 2015 Kabul attacks | Crimes | 658 | 0 / 4 | pending |
| 656 | 2019 Kabul mosque bombing | Crimes | 658 | 0 / 4 | pending |
| 282 | Black Dragons (gang) | Groups | 659 | 0 / 4 | pending |
| 895 | Ganghwa massacre | Crimes | 659 | 1 / 4 | pending |
| 438 | John 'Chow' Hayes | Criminals | 661 | 0 / 4 | pending |
| 542 | Domenico Libri | Criminals | 663 | 1 / 4 | pending |
| 957 | Accomarca massacre | Crimes | 663 | 0 / 4 | pending |
| 758 | Baylor Massacre | Crimes | 664 | 4 / 4 | pending |
| 345 | Michael Cobb | Criminals | 665 | 2 / 4 | pending |
| 821 | Gondrand massacre | Crimes | 665 | 2 / 4 | pending |
| 1105 | Lawrence Bishnoi | Criminals | 665 | 1 / 4 | pending |
| 558 | Girolamo Molè | Criminals | 666 | 0 / 4 | pending |
| 898 | Lari massacre | Crimes | 667 | 0 / 4 | pending |
| 615 | Lusciano | Criminals | 670 | 2 / 4 | pending |
| 1021 | 2010 San Fernando massacre | Crimes | 670 | 0 / 4 | pending |
| 1042 | La Vega raid | Crimes | 670 | 0 / 5 | pending |
| 1050 | Breed Motorcycle Club | Groups | 670 | 2 / 4 | pending |
| 1087 | Sadistic Souls Motorcycle Club | Groups | 670 | 0 / 4 | pending |
| 307 | Australian Defence League | Groups | 672 | 0 / 4 | pending |
| 859 | Foibe massacres | Crimes | 675 | 3 / 5 | pending |
| 908 | Binh Tai Massacre | Crimes | 675 | 0 / 4 | pending |
| 1000 | El Aro Massacre | Crimes | 675 | 0 / 5 | pending |
| 918 | El Halconazo | Crimes | 676 | 0 / 4 | pending |
| 1089 | Satudarah | Groups | 677 | 1 / 4 | pending |
| 1108 | Francis Ford Coppola | Blog | 677 | 4 / 4 | pending |
| 770 | American Ranch massacre | Crimes | 678 | 0 / 4 | pending |
| 1019 | 2009 Fort Hood shooting | Crimes | 679 | 0 / 4 | pending |
| 266 | Logan Heights Gang | Groups | 680 | 0 / 4 | pending |
| 739 | Madulla | Crimes | 680 | 3 / 4 | pending |
| 1117 | Sábado de mierda | Groups | 680 | 2 / 4 | pending |
| 922 | Ezeiza massacre | Crimes | 681 | 0 / 4 | pending |
| 666 | June 2020 Afghanistan attacks | Crimes | 682 | 0 / 4 | pending |
| 742 | Naousa massacre | Crimes | 682 | 0 / 4 | pending |
| 630 | 11 June 2013 Kabul bombing | Crimes | 685 | 0 / 5 | pending |
| 920 | Lod Airport massacre | Crimes | 691 | 0 / 4 | pending |
| 909 | Bình Hòa massacre | Crimes | 693 | 0 / 4 | pending |
| 831 | Ip massacre | Crimes | 695 | 3 / 4 | pending |
| 878 | Balad al-Shaykh massacre | Crimes | 695 | 1 / 5 | pending |
| 261 | Avenues (gang) | Groups | 696 | 0 / 5 | pending |
| 867 | Ochota massacre | Crimes | 696 | 0 / 5 | pending |
| 1006 | Columbine High School massacre | Crimes | 696 | 0 / 5 | pending |
| 340 | Benjamin Benny | Criminals | 699 | 0 / 4 | pending |
| 372 | Brian Keith Jones | Criminals | 700 | 0 / 4 | pending |
| 1075 | Notorious Motorcycle Club (Australia) | Groups | 700 | 2 / 5 | pending |
| 264 | El Monte Flores | Groups | 702 | 0 / 5 | pending |
| 870 | Chenogne massacre | Crimes | 702 | 0 / 5 | pending |
| 893 | Goyang Geumjeong Cave massacre | Crimes | 702 | 1 / 4 | pending |
| 299 | Moonshiners Motorcycle Club | Groups | 703 | 1 / 4 | pending |
| 422 | Theresa Lawson | Criminals | 703 | 0 / 5 | pending |
| 969 | Monrovia Church massacre | Crimes | 703 | 0 / 4 | pending |
| 1001 | Luxor massacre | Crimes | 705 | 0 / 5 | pending |
| 1100 | Solomon Molcho | Criminals | 706 | 1 / 4 | pending |
| 389 | Michael McAuliffe (drug trafficker) | Criminals | 707 | 0 / 4 | pending |
| 474 | Bilal Skaf | Criminals | 707 | 0 / 5 | pending |
| 989 | Greysteel massacre | Crimes | 707 | 0 / 4 | pending |
| 994 | Beit Lid suicide bombing | Crimes | 707 | 0 / 4 | pending |
| 1079 | Peckerwood | Groups | 709 | 3 / 5 | pending |
| 657 | 1 July 2019 Kabul attack | Crimes | 710 | 0 / 5 | pending |
| 913 | Phong Nhị and Phong Nhất massacre | Crimes | 711 | 0 / 4 | pending |
| 521 | G. Janardhana Reddy | Criminals | 714 | 0 / 4 | pending |
| 820 | Ranquil massacre | Crimes | 714 | 3 / 4 | pending |
| 411 | Mark Moran (criminal) | Criminals | 715 | 0 / 4 | pending |
| 525 | Umesh Singh Kushwaha | Criminals | 716 | 0 / 5 | pending |
| 1078 | Pagan's Motorcycle Club | Groups | 716 | 2 / 5 | pending |
| 905 | Oran massacre of 1962 | Crimes | 717 | 0 / 4 | pending |
| 1086 | Rock Machine | Groups | 717 | 3 / 5 | pending |
| 325 | Diablos Motorcycle Club (founded 1999) | Groups | 718 | 0 / 4 | pending |
| 1088 | Satan's Choice Motorcycle Club | Groups | 719 | 0 / 4 | pending |
| 358 | Karyn Paluzzano | Criminals | 720 | 1 / 5 | pending |
| 588 | Giuseppe Coluccio | Criminals | 722 | 5 / 5 | pending |
| 755 | Erfurt massacre (1349) | Crimes | 723 | 1 / 5 | pending |
| 756 | Lamey Island Massacre | Crimes | 724 | 0 / 4 | pending |
| 233 | Armenian Power | Groups | 725 | 0 / 4 | pending |
| 520 | Ramalinga Raju | Criminals | 725 | 0 / 4 | pending |
| 641 | April 2016 Kabul attack | Crimes | 725 | 0 / 5 | pending |
| 580 | Amsterdam | Criminals | 726 | 1 / 4 | pending |
| 344 | Bob Woods (politician) | Criminals | 728 | 1 / 4 | pending |
| 523 | Jagdish Mahto | Criminals | 728 | 0 / 4 | pending |
| 986 | Sivas massacre | Crimes | 729 | 0 / 5 | pending |
| 1081 | Popeye Moto Club | Groups | 733 | 2 / 5 | pending |
| 664 | Kabul gurdwara attack | Crimes | 734 | 0 / 6 | pending |
| 948 | 1982 Hama massacre | Crimes | 734 | 0 / 4 | pending |
| 524 | Abhay Kushwaha | Criminals | 735 | 0 / 4 | pending |
| 766 | Bear River Massacre | Crimes | 735 | 0 / 4 | pending |
| 912 | Jabidah massacre | Crimes | 736 | 0 / 4 | pending |
| 1023 | Mekong River massacre | Crimes | 736 | 0 / 4 | pending |
| 674 | 2021 Kabul school bombing | Crimes | 738 | 0 / 5 | pending |
| 629 | April 2012 Afghanistan attacks | Crimes | 739 | 0 / 5 | pending |
| 687 | Dashte Barchi | Criminals | 739 | 0 / 5 | pending |
| 1116 | Delhi Crime | Blog | 739 | 0 / 5 | pending |
| 1122 | Delia Owens | Blog | 739 | 0 / 5 | pending |
| 983 | Brown's Chicken massacre | Crimes | 741 | 0 / 5 | pending |
| 546 | Umberto Bellocco | Criminals | 745 | 0 / 4 | pending |
| 1011 | Passover massacre | Crimes | 746 | 0 / 5 | pending |
| 537 | Attilio Cubeddu | Criminals | 747 | 0 / 5 | pending |
| 1003 | Acteal massacre | Crimes | 749 | 0 / 5 | pending |
| 1044 | Solhan and Tadaryat massacres | Crimes | 749 | 0 / 5 | pending |
| 1127 | Jussie Smollett | Criminals | 749 | 0 / 5 | pending |
| 988 | Saint James Church massacre | Crimes | 751 | 0 / 4 | pending |
| 1017 | Virginia Tech shooting | Crimes | 751 | 0 / 4 | pending |
| 1020 | Maguindanao massacre | Crimes | 751 | 0 / 4 | pending |
| 343 | Frank Ford (Australian politician) | Criminals | 752 | 1 / 4 | pending |
| 481 | Bandali Debs | Criminals | 752 | 0 / 5 | pending |
| 1009 | Osaka school massacre | Crimes | 753 | 0 / 5 | pending |
| 579 | Paolo Di Lauro | Criminals | 754 | 0 / 4 | pending |
| 265 | Florencia 13 | Groups | 755 | 0 / 5 | pending |
| 936 | Karantina massacre | Crimes | 755 | 0 / 4 | pending |
| 519 | Natwarlal | Criminals | 757 | 0 / 4 | pending |
| 541 | Carmine Alfieri | Criminals | 759 | 0 / 4 | pending |
| 331 | Red Devils Motorcycle Club | Groups | 760 | 0 / 4 | pending |
| 714 | Junkersdorf massacre | Crimes | 760 | 4 / 4 | pending |
| 425 | David Parker (Australian politician) | Criminals | 764 | 0 / 5 | pending |
| 531 | Anand Mohan Singh | Criminals | 764 | 0 / 5 | pending |
| 624 | February 2010 Kabul attack | Crimes | 765 | 0 / 5 | pending |
| 876 | Rawagede massacre | Crimes | 766 | 0 / 5 | pending |
| 926 | Siege of Tel al-Zaatar | Crimes | 766 | 0 / 4 | pending |
| 765 | Nueces massacre | Crimes | 773 | 0 / 4 | pending |
| 363 | Keith Faure | Criminals | 774 | 0 / 5 | pending |
| 956 | Anuradhapura massacre | Crimes | 774 | 0 / 4 | pending |
| 1010 | Gulbarg Society massacre | Crimes | 774 | 0 / 5 | pending |
| 1034 | Nduga massacre | Crimes | 775 | 0 / 5 | pending |
| 1065 | Highwaymen Motorcycle Club | Groups | 775 | 1 / 4 | pending |
| 513 | Mehul Choksi | Criminals | 780 | 0 / 5 | pending |
| 952 | 1983 Lucanamarca massacre | Crimes | 781 | 0 / 4 | pending |
| 660 | 17 August 2019 Kabul bombing | Crimes | 782 | 0 / 5 | pending |
| 879 | Deir Yassin massacre | Crimes | 782 | 0 / 5 | pending |
| 486 | Perth | Criminals | 783 | 3 / 5 | pending |
| 865 | Wola massacre | Crimes | 784 | 0 / 5 | pending |
| 872 | Bleiburg repatriations | Crimes | 785 | 0 / 5 | pending |
| 923 | Battle of Jolo (1974) | Crimes | 788 | 0 / 4 | pending |
| 991 | Shell House massacre | Crimes | 789 | 0 / 4 | pending |
| 404 | Kath Pettingill | Criminals | 791 | 0 / 4 | pending |
| 626 | 2011 Inter-Continental Hotel Kabul attack | Crimes | 791 | 0 / 4 | pending |
| 885 | Safsaf massacre | Crimes | 793 | 0 / 4 | pending |
| 240 | Rollin' 60s Neighborhood Crips | Groups | 795 | 0 / 6 | pending |
| 667 | July 2020 Afghanistan attacks | Crimes | 795 | 0 / 6 | pending |
| 803 | Perry massacre | Crimes | 797 | 1 / 4 | pending |
| 586 | Polizia di Stato | Criminals | 800 | 4 / 4 | pending |
| 941 | Golden Dragon massacre | Crimes | 801 | 1 / 4 | pending |
| 419 | Laurie Connell | Criminals | 803 | 0 / 4 | pending |
| 942 | Coastal Road massacre | Crimes | 803 | 0 / 4 | pending |
| 996 | Srebrenica massacre | Crimes | 803 | 0 / 6 | pending |
| 1022 | 2011 Norway attacks | Crimes | 803 | 0 / 4 | pending |
| 593 | Patrizio Bosti | Criminals | 804 | 0 / 4 | pending |
| 902 | Matikhrü massacre | Crimes | 804 | 0 / 4 | pending |
| 1016 | 2006 Qana airstrike | Crimes | 804 | 0 / 4 | pending |
| 683 | Supreme Court of the Islamic Emirate of Afghanistan | Criminals | 807 | 0 / 5 | pending |
| 869 | Malmedy massacre | Crimes | 807 | 0 / 5 | pending |
| 229 | Frank Scalice | Criminals | 808 | 0 / 4 | pending |
| 437 | Mick Gatto | Criminals | 808 | 0 / 4 | pending |
| 466 | Anthony Perish | Criminals | 811 | 0 / 5 | pending |
| 1106 | Nipsey Hussle | Blog | 812 | 2 / 4 | pending |
| 702 | 1033 Fez massacre | Crimes | 813 | 0 / 4 | pending |
| 754 | Cao Cao's invasion of Xu Province | Crimes | 813 | 0 / 4 | pending |
| 864 | Distomo massacre | Crimes | 814 | 0 / 5 | pending |
| 960 | Aranthalawa massacre | Crimes | 814 | 0 / 4 | pending |
| 1107 | Monica Lewinsky | Blog | 814 | 2 / 4 | pending |
| 382 | Andrew Chan | Criminals | 815 | 0 / 4 | pending |
| 315 | 'Ndrangheta | Groups | 816 | 0 / 4 | pending |
| 961 | Pınarcık massacre | Crimes | 817 | 0 / 4 | pending |
| 239 | Rollin' 30s Harlem Crips | Groups | 818 | 0 / 4 | pending |
| 676 | 2021 Kabul hospital attack | Crimes | 818 | 0 / 4 | pending |
| 583 | Giuseppe Bellocco | Criminals | 819 | 0 / 4 | pending |
| 244 | Fresno Bulldogs | Groups | 820 | 0 / 5 | pending |
| 260 | 38th Street gang | Groups | 820 | 0 / 5 | pending |
| 303 | Snakehead (gang) | Groups | 821 | 0 / 5 | pending |
| 698 | Zabiullah Mujahid | Criminals | 821 | 0 / 4 | pending |
| 1015 | Mahmudiyah rape and killings | Crimes | 822 | 0 / 5 | pending |
| 998 | Qana massacre | Crimes | 824 | 0 / 5 | pending |
| 381 | Bali Nine | Criminals | 825 | 0 / 4 | pending |
| 843 | Sook Ching | Crimes | 825 | 3 / 5 | pending |
| 982 | Carandiru massacre | Crimes | 825 | 0 / 5 | pending |
| 310 | The Carlton Crew | Groups | 826 | 0 / 4 | pending |
| 353 | Rex Jackson | Criminals | 826 | 0 / 5 | pending |
| 932 | Kiryat Shmona massacre | Crimes | 827 | 0 / 4 | pending |
| 1047 | Bacchus Motorcycle Club | Groups | 828 | 0 / 5 | pending |
| 847 | Qissa Khwani massacre | Crimes | 829 | 4 / 5 | pending |
| 534 | Surajbhan Singh | Criminals | 830 | 0 / 6 | pending |
| 782 | 1900 Amur anti-Chinese pogroms | Crimes | 830 | 0 / 4 | pending |
| 304 | Sun Yee On | Groups | 831 | 2 / 4 | pending |
| 356 | Milton Orkopoulos | Criminals | 831 | 0 / 5 | pending |
| 253 | Norteños | Groups | 832 | 0 / 4 | pending |
| 462 | Craig Minogue | Criminals | 832 | 0 / 5 | pending |
| 567 | Antonino Giuffrè | Criminals | 835 | 0 / 4 | pending |
| 647 | March 2017 Kabul attack | Crimes | 836 | 0 / 5 | pending |
| 349 | Peter Howe (New South Wales politician) | Criminals | 837 | 1 / 4 | pending |
| 514 | Rajkissore Dutt | Criminals | 837 | 0 / 4 | pending |
| 613 | Sardinia | Criminals | 838 | 2 / 4 | pending |
| 1136 | India at the 2022 Commonwealth Games | Blog | 839 | 0 / 4 | pending |
| 267 | OVS (gang) | Groups | 840 | 0 / 5 | pending |
| 460 | Martin Leach (murderer) | Criminals | 840 | 0 / 5 | pending |
| 625 | May 2010 Kabul bombing | Crimes | 840 | 1 / 4 | pending |
| 243 | Devils Diciples | Groups | 841 | 1 / 4 | pending |
| 746 | Piet Retief Delegation massacre | Crimes | 844 | 0 / 4 | pending |
| 871 | Manila massacre | Crimes | 844 | 0 / 5 | pending |
| 1008 | Nepalese royal massacre | Crimes | 844 | 0 / 5 | pending |
| 1029 | 2014 Peshawar school massacre | Crimes | 845 | 0 / 5 | pending |
| 1068 | Kings Crew Motorcycle Club | Groups | 845 | 2 / 4 | pending |
| 648 | May 2017 Kabul bombing | Crimes | 846 | 0 / 5 | pending |
| 733 | Olowalu Massacre | Crimes | 846 | 2 / 4 | pending |
| 387 | Warren Fellows | Criminals | 848 | 0 / 4 | pending |
| 300 | Playboys (gang) | Groups | 849 | 0 / 5 | pending |
| 783 | Leliefontein massacre | Crimes | 849 | 2 / 4 | pending |
| 943 | Marichjhapi massacre | Crimes | 850 | 0 / 4 | pending |
| 1028 | Sinjar massacre | Crimes | 851 | 0 / 5 | pending |
| 336 | Outlaws Motorcycle Club | Groups | 853 | 3 / 4 | pending |
| 468 | Joseph Schwab | Criminals | 853 | 3 / 5 | pending |
| 487 | Leonard Fraser | Criminals | 853 | 0 / 5 | pending |
| 582 | Corleone | Criminals | 853 | 0 / 4 | pending |
| 659 | 7 August 2019 Kabul bombing | Crimes | 853 | 0 / 5 | pending |
| 1124 | Thor: Love and Thunder | Blog | 853 | 0 / 5 | pending |
| 495 | Eddie Leonski | Criminals | 855 | 2 / 4 | pending |
| 910 | Asaba massacre | Crimes | 855 | 0 / 4 | pending |
| 501 | Martha Needle | Criminals | 856 | 4 / 4 | pending |
| 997 | Dunblane massacre | Crimes | 856 | 0 / 5 | pending |
| 1004 | Omagh bombing | Crimes | 857 | 0 / 5 | pending |
| 759 | Peterloo Massacre | Crimes | 858 | 0 / 4 | pending |
| 949 | Sabra and Shatila massacre | Crimes | 858 | 0 / 4 | pending |
| 297 | Pirus | Groups | 859 | 0 / 4 | pending |
| 790 | Abschwangen massacre | Crimes | 859 | 5 / 5 | pending |
| 973 | Barrios Altos massacre | Crimes | 860 | 0 / 4 | pending |
| 860 | Huta Pieniacka massacre | Crimes | 861 | 2 / 5 | pending |
| 946 | Tadmor Prison | Crimes | 862 | 0 / 4 | pending |
| 578 | Toronto | Criminals | 863 | 1 / 4 | pending |
| 231 | Ciro Terranova | Criminals | 864 | 0 / 4 | pending |
| 875 | Portella della Ginestra massacre | Crimes | 864 | 1 / 5 | pending |
| 380 | Peter Scully | Criminals | 865 | 0 / 4 | pending |
| 516 | Vijay Mallya | Criminals | 865 | 0 / 5 | pending |
| 316 | Pettingill family | Groups | 866 | 0 / 4 | pending |
| 321 | Hammerskins | Groups | 867 | 0 / 4 | pending |
| 897 | Geochang massacre | Crimes | 867 | 0 / 4 | pending |
| 302 | Sam Gor | Groups | 868 | 0 / 5 | pending |
| 781 | Battle of Mazocoba | Crimes | 868 | 0 / 4 | pending |
| 1026 | August 2013 Rabaa massacre | Crimes | 869 | 0 / 5 | pending |
| 350 | Frank Smith (New South Wales politician) | Criminals | 870 | 0 / 5 | pending |
| 547 | Pasquale Condello | Criminals | 870 | 0 / 4 | pending |
| 1132 | Anne Heche | Blog | 870 | 0 / 4 | pending |
| 1103 | Robb Elementary School shooting | Crimes | 871 | 2 / 4 | pending |
| 587 | Edoardo Contini | Criminals | 872 | 0 / 4 | pending |
| 1121 | The Batman (film) | Blog | 873 | 0 / 5 | pending |
| 238 | Grape Street Watts Crips | Groups | 875 | 1 / 4 | pending |
| 433 | Woolworths Supermarkets | Criminals | 877 | 0 / 4 | pending |
| 980 | La Cantuta massacre | Crimes | 878 | 0 / 4 | pending |
| 427 | Rene Rivkin | Criminals | 879 | 0 / 4 | pending |
| 581 | Bernardo Provenzano | Criminals | 879 | 0 / 4 | pending |
| 880 | Hadassah medical convoy massacre | Crimes | 879 | 1 / 5 | pending |
| 478 | Snowtown murders | Criminals | 881 | 0 / 5 | pending |
| 911 | Massacre at Huế | Crimes | 883 | 0 / 4 | pending |
| 320 | Soldiers of Odin | Groups | 884 | 0 / 4 | pending |
| 569 | Ridderkerk | Criminals | 884 | 3 / 4 | pending |
| 673 | December 2020 Afghanistan attacks | Crimes | 884 | 0 / 5 | pending |
| 990 | Cave of the Patriarchs massacre | Crimes | 884 | 0 / 4 | pending |
| 945 | Gwangju Uprising | Crimes | 886 | 0 / 4 | pending |
| 555 | Giorgio De Stefano (1948) | Criminals | 887 | 0 / 4 | pending |
| 503 | Martha Rendell | Criminals | 888 | 2 / 5 | pending |
| 816 | Les Cayes massacre | Crimes | 888 | 0 / 5 | pending |
| 1061 | Head Hunters Motorcycle Club | Groups | 889 | 0 / 5 | pending |
| 354 | Barry Morris | Criminals | 891 | 2 / 4 | pending |
| 485 | Claremont, Western Australia | Criminals | 892 | 3 / 4 | pending |
| 678 | 2009 bombing of Indian embassy in Kabul | Crimes | 895 | 0 / 4 | pending |
| 747 | Weenen massacre | Crimes | 895 | 0 / 4 | pending |
| 836 | NKVD prisoner massacres | Crimes | 897 | 4 / 4 | pending |
| 924 | Ma'alot massacre | Crimes | 897 | 0 / 4 | pending |
| 717 | Portadown massacre | Crimes | 899 | 1 / 5 | pending |
| 254 | Peckerwood | Groups | 901 | 0 / 4 | pending |
| 359 | Adam Marshall | Criminals | 903 | 0 / 5 | pending |
| 854 | Naliboki massacre | Crimes | 903 | 2 / 5 | pending |
| 337 | Rebels Motorcycle Club | Groups | 904 | 0 / 4 | pending |
| 668 | August 2020 Afghanistan attacks | Crimes | 904 | 0 / 5 | pending |
| 937 | Damour massacre | Crimes | 904 | 0 / 4 | pending |
| 933 | Palimbang massacre | Crimes | 905 | 0 / 4 | pending |
| 399 | Martin Stephens (drug smuggler) | Criminals | 908 | 0 / 5 | pending |
| 862 | Ascq massacre | Crimes | 909 | 0 / 5 | pending |
| 570 | Francesco Mallardo | Criminals | 910 | 4 / 4 | pending |
| 741 | Chios massacre | Crimes | 910 | 0 / 4 | pending |
| 573 | Vito Roberto Palazzolo | Criminals | 912 | 0 / 4 | pending |
| 416 | Alan Bond | Criminals | 915 | 0 / 5 | pending |
| 556 | Polverino clan | Criminals | 915 | 3 / 4 | pending |
| 469 | Neddy Smith | Criminals | 918 | 0 / 5 | pending |
| 916 | Kent State shootings | Crimes | 918 | 0 / 4 | pending |
| 365 | Gregory David Roberts | Criminals | 920 | 1 / 4 | pending |
| 418 | Premier of Western Australia | Criminals | 920 | 0 / 5 | pending |
| 822 | Paracuellos massacres | Crimes | 921 | 4 / 4 | pending |
| 1139 | Fate: The Winx Saga | Blog | 921 | 0 / 4 | pending |
| 314 | Moran family | Groups | 922 | 0 / 4 | pending |
| 296 | Bloods | Groups | 923 | 0 / 5 | pending |
| 561 | Francesco Schiavone | Criminals | 923 | 0 / 4 | pending |
| 727 | Affair at Little Egg Harbor | Crimes | 927 | 0 / 4 | pending |
| 566 | Maria Licciardi | Criminals | 929 | 0 / 4 | pending |
| 255 | Nazi Lowriders | Groups | 932 | 0 / 4 | pending |
| 502 | Derek Percy | Criminals | 932 | 0 / 5 | pending |
| 863 | Oradour-sur-Glane massacre | Crimes | 932 | 0 / 5 | pending |
| 364 | Victor Peirce | Criminals | 933 | 0 / 5 | pending |
| 929 | Sharpeville massacre | Crimes | 933 | 0 / 4 | pending |
| 250 | Menace of Destruction | Groups | 934 | 1 / 4 | pending |
| 306 | Antipodean Resistance | Groups | 938 | 0 / 6 | pending |
| 393 | Gerald Ridsdale | Criminals | 938 | 0 / 4 | pending |
| 274 | Venice 13 | Groups | 939 | 1 / 4 | pending |
| 595 | Giuseppe Setola | Criminals | 939 | 0 / 5 | pending |
| 874 | February 28 incident | Crimes | 939 | 0 / 5 | pending |
| 695 | Bismillah Khan Mohammadi | Criminals | 940 | 0 / 5 | pending |
| 721 | Batih massacre | Crimes | 941 | 1 / 4 | pending |
| 1041 | Axum massacre | Crimes | 941 | 0 / 5 | pending |
| 348 | Steve Irons | Criminals | 942 | 0 / 4 | pending |
| 428 | Christopher Skase | Criminals | 942 | 0 / 4 | pending |
| 731 | Pyle's Massacre | Crimes | 943 | 0 / 4 | pending |
| 1025 | Houla massacre | Crimes | 943 | 0 / 5 | pending |
| 827 | Seguro Obrero massacre | Crimes | 944 | 0 / 4 | pending |
| 564 | Luigi Giuliano | Criminals | 945 | 0 / 4 | pending |
| 730 | Sugarloaf massacre | Crimes | 945 | 1 / 5 | pending |
| 592 | Vincenzo Licciardi | Criminals | 947 | 0 / 4 | pending |
| 985 | Waco siege | Crimes | 949 | 0 / 5 | pending |
| 492 | Matthew James Harris | Criminals | 950 | 2 / 4 | pending |
| 383 | Michael Czugaj | Criminals | 951 | 0 / 5 | pending |
| 600 | Polistena | Criminals | 953 | 1 / 4 | pending |
| 395 | Si Yi Chen | Criminals | 954 | 0 / 5 | pending |
| 955 | Dujail Massacre | Crimes | 956 | 0 / 4 | pending |
| 371 | Robert Hughes (actor) | Criminals | 957 | 0 / 4 | pending |
| 515 | Rajat Gupta | Criminals | 957 | 3 / 5 | pending |
| 571 | Giuseppe Morabito | Criminals | 958 | 4 / 5 | pending |
| 762 | Crabb massacre | Crimes | 960 | 3 / 4 | pending |
| 712 | Clandeboye massacre | Crimes | 962 | 2 / 4 | pending |
| 964 | Queen Street massacre | Crimes | 962 | 0 / 4 | pending |
| 234 | Bounty Hunter Watts Bloods | Groups | 963 | 0 / 4 | pending |
| 497 | Berrima, New South Wales | Criminals | 964 | 2 / 5 | pending |
| 298 | Diablos Motorcycle Club | Groups | 965 | 2 / 4 | pending |
| 562 | Giuseppe Piromalli (born 1945) | Criminals | 966 | 0 / 4 | pending |
| 643 | July 2016 Kabul bombing | Crimes | 967 | 0 / 5 | pending |
| 873 | Sétif and Guelma massacre | Crimes | 967 | 0 / 5 | pending |
| 1024 | Kandahar massacre | Crimes | 967 | 0 / 4 | pending |
| 293 | 18th Street gang | Groups | 969 | 0 / 5 | pending |
| 394 | Dennis Allen (criminal) | Criminals | 969 | 1 / 4 | pending |
| 887 | Rengat massacre | Crimes | 969 | 0 / 4 | pending |
| 352 | Thomas Ley | Criminals | 970 | 0 / 4 | pending |
| 608 | Sperone | Criminals | 970 | 3 / 4 | pending |
| 370 | Rolf Harris | Criminals | 975 | 0 / 4 | pending |
| 691 | Jowzjan Province | Criminals | 975 | 0 / 4 | pending |
| 837 | Kamianets-Podilskyi massacre | Crimes | 975 | 4 / 4 | pending |
| 440 | Lenny McPherson | Criminals | 979 | 2 / 4 | pending |
| 769 | Centralia Massacre (Missouri) | Crimes | 979 | 0 / 5 | pending |
| 259 | Beltrán-Leyva Organization | Groups | 980 | 0 / 5 | pending |
| 715 | Indian massacre of 1622 | Crimes | 980 | 0 / 5 | pending |
| 889 | Bodo League massacre | Crimes | 980 | 0 / 4 | pending |
| 953 | Wagalla massacre | Crimes | 980 | 0 / 4 | pending |
| 1049 | Blue Angels Motorcycle Club | Groups | 982 | 0 / 5 | pending |
| 1013 | Andijan massacre | Crimes | 986 | 0 / 5 | pending |
| 540 | Giovanni Motisi | Criminals | 988 | 1 / 4 | pending |
| 522 | Abdul Karim Telgi | Criminals | 989 | 0 / 4 | pending |
| 853 | Khatyn massacre | Crimes | 989 | 2 / 5 | pending |
| 708 | Brussels massacre | Crimes | 990 | 0 / 4 | pending |
| 974 | Santa Cruz massacre | Crimes | 991 | 0 / 4 | pending |
| 738 | Fort Mims massacre | Crimes | 994 | 2 / 4 | pending |
| 603 | Reggio Calabria | Criminals | 995 | 2 / 4 | pending |
| 795 | Menemen massacre | Crimes | 997 | 1 / 4 | pending |
| 294 | Abergil crime family | Groups | 998 | 0 / 5 | pending |
| 774 | Rock Springs massacre | Crimes | 998 | 0 / 5 | pending |
| 305 | Albanian mafia | Groups | 999 | 2 / 5 | pending |

## Tier 2: 1,000 to 1,199 words

| Post | Title | Category | Words | Sources (homepage-only / total) | Status |
|---|---|---|---|---|---|
| 799 | Gando massacre | Crimes | 1000 | 2 / 5 | pending |
| 848 | Parsley massacre | Crimes | 1001 | 4 / 5 | pending |
| 568 | Cannes | Criminals | 1002 | 0 / 4 | pending |
| 346 | Andrew Theophanous | Criminals | 1003 | 0 / 5 | pending |
| 507 | Truro murders | Criminals | 1003 | 1 / 4 | pending |
| 777 | Massacre of Italians at Aigues-Mortes | Crimes | 1003 | 0 / 4 | pending |
| 919 | Bloody Sunday (1972) | Crimes | 1008 | 0 / 4 | pending |
| 1039 | 2019 El Paso shooting | Crimes | 1009 | 0 / 5 | pending |
| 1098 | Zulus Motorcycle Club | Groups | 1009 | 2 / 5 | pending |
| 415 | Hajnal Ban | Criminals | 1013 | 0 / 4 | pending |
| 780 | Lattimer massacre | Crimes | 1015 | 4 / 5 | pending |
| 773 | Los Angeles Chinese massacre of 1871 | Crimes | 1017 | 0 / 4 | pending |
| 947 | El Mozote massacre | Crimes | 1017 | 0 / 4 | pending |
| 557 | Pietro Aglieri | Criminals | 1018 | 3 / 4 | pending |
| 565 | Nuvoletta clan | Criminals | 1019 | 0 / 5 | pending |
| 328 | Comanchero Motorcycle Club | Groups | 1020 | 0 / 4 | pending |
| 743 | Kasos Massacre | Crimes | 1023 | 0 / 4 | pending |
| 1035 | Christchurch mosque shootings | Crimes | 1028 | 1 / 5 | pending |
| 505 | Lindsey Robert Rose | Criminals | 1029 | 0 / 4 | pending |
| 544 | Rosetta Cutolo | Criminals | 1029 | 0 / 5 | pending |
| 309 | Bulgarian mafia | Groups | 1030 | 0 / 5 | pending |
| 374 | Brothers Hospitallers of Saint John of God | Criminals | 1030 | 3 / 4 | pending |
| 543 | Palermo | Criminals | 1030 | 1 / 5 | pending |
| 318 | Russian mafia | Groups | 1033 | 0 / 4 | pending |
| 899 | Qibya massacre | Crimes | 1034 | 0 / 4 | pending |
| 575 | Ústí nad Labem | Criminals | 1035 | 0 / 4 | pending |
| 752 | Pottawatomie massacre | Crimes | 1035 | 2 / 4 | pending |
| 992 | Loughinisland massacre | Crimes | 1035 | 0 / 4 | pending |
| 1062 | Hells Angels | Groups | 1035 | 2 / 5 | pending |
| 295 | Bahala Na Gang | Groups | 1036 | 0 / 4 | pending |
| 616 | 2008 Kabul Serena Hotel attack | Crimes | 1036 | 1 / 4 | pending |
| 384 | Matthew Norman | Criminals | 1037 | 0 / 5 | pending |
| 510 | David Hicks | Criminals | 1037 | 2 / 5 | pending |
| 323 | Lebanese mafia | Groups | 1039 | 0 / 4 | pending |
| 1130 | Salman Rushdie | Blog | 1040 | 0 / 5 | pending |
| 1032 | Orlando nightclub shooting | Crimes | 1041 | 0 / 5 | pending |
| 693 | National Directorate of Security | Criminals | 1044 | 0 / 5 | pending |
| 464 | Bradley John Murdoch | Criminals | 1045 | 0 / 4 | pending |
| 670 | October 2020 Afghanistan attacks | Crimes | 1045 | 0 / 5 | pending |
| 907 | Indonesian mass killings of 1965–66 | Crimes | 1046 | 0 / 4 | pending |
| 1131 | Stabbing of Salman Rushdie | Crimes | 1046 | 0 / 5 | pending |
| 978 | Maraga massacre | Crimes | 1049 | 0 / 4 | pending |
| 681 | Kandahar | Criminals | 1052 | 0 / 5 | pending |
| 857 | Battle of Wake Island | Crimes | 1054 | 2 / 5 | pending |
| 1036 | Kharqamar incident | Crimes | 1054 | 0 / 5 | pending |
| 397 | Tan Duc Thanh Nguyen | Criminals | 1056 | 0 / 5 | pending |
| 402 | Thailand | Criminals | 1056 | 0 / 5 | pending |
| 1014 | Haditha massacre | Crimes | 1056 | 0 / 5 | pending |
| 740 | Navarino massacre | Crimes | 1057 | 1 / 4 | pending |
| 1113 | 2022 Sri Lankan protests | Blog | 1058 | 0 / 4 | pending |
| 281 | Big Circle Gang | Groups | 1060 | 0 / 4 | pending |
| 379 | Peter Scully | Criminals | 1061 | 0 / 4 | pending |
| 894 | Sinchon Massacre | Crimes | 1061 | 0 / 4 | pending |
| 881 | Kfar Etzion massacre | Crimes | 1062 | 0 / 5 | pending |
| 735 | First Massacre of Machecoul | Crimes | 1064 | 1 / 4 | pending |
| 921 | Munich massacre | Crimes | 1064 | 0 / 4 | pending |
| 704 | Massacre of the Latins | Crimes | 1066 | 0 / 5 | pending |
| 1123 | Murder of Logan Mwangi | Crimes | 1066 | 0 / 5 | pending |
| 751 | Haun's Mill massacre | Crimes | 1069 | 1 / 4 | pending |
| 414 | HIH Insurance | Criminals | 1072 | 0 / 5 | pending |
| 1109 | Sfera Ebbasta | Blog | 1072 | 1 / 5 | pending |
| 718 | Storming of Bolton | Crimes | 1073 | 0 / 5 | pending |
| 744 | Cutthroat Gap massacre | Crimes | 1074 | 1 / 4 | pending |
| 846 | Frog Lake Massacre | Crimes | 1076 | 2 / 5 | pending |
| 934 | Miami Showband killings | Crimes | 1078 | 0 / 4 | pending |
| 1046 | Shedden massacre | Groups | 1078 | 0 / 5 | pending |
| 60 | Cleophus Cooksey Jr. | Criminals | 1079 | 0 / 5 | pending |
| 99 | Abu Khattab al-Tunisi | Criminals | 1082 | 0 / 5 | pending |
| 617 | 2008 bombing of Indian embassy in Kabul | Crimes | 1083 | 1 / 4 | pending |
| 842 | Battle of Ambon | Crimes | 1084 | 2 / 5 | pending |
| 760 | Goliad massacre | Crimes | 1085 | 1 / 4 | pending |
| 844 | Cypress Hills Massacre | Crimes | 1085 | 2 / 5 | pending |
| 1126 | Guilherme de Pádua | Blog | 1088 | 0 / 5 | pending |
| 292 | Nuestra Familia | Groups | 1089 | 1 / 5 | pending |
| 1040 | 2020 Lekki shooting | Crimes | 1093 | 0 / 5 | pending |
| 251 | Mongols Motorcycle Club | Groups | 1094 | 0 / 4 | pending |
| 688 | Médecins Sans Frontières | Criminals | 1094 | 0 / 5 | pending |
| 407 | Andrew Veniamin | Criminals | 1095 | 1 / 4 | pending |
| 1045 | 2021 Nagaland killings | Crimes | 1095 | 0 / 6 | pending |
| 165 | Benedetto Aloi | Criminals | 1096 | 0 / 6 | pending |
| 553 | Leoluca Bagarella | Criminals | 1097 | 0 / 4 | pending |
| 725 | Massacre of St George's Fields | Crimes | 1097 | 1 / 4 | pending |
| 1031 | November 2015 Paris attacks | Crimes | 1097 | 0 / 5 | pending |
| 1033 | Inn Din massacre | Crimes | 1098 | 0 / 5 | pending |
| 361 | Brenden Abbott | Criminals | 1099 | 2 / 5 | pending |
| 248 | Latin Kings (gang) | Groups | 1104 | 3 / 5 | pending |
| 527 | Jagannath Mishra | Criminals | 1106 | 0 / 5 | pending |
| 396 | Renae Lawrence | Criminals | 1109 | 0 / 5 | pending |
| 449 | Martin Bryant | Criminals | 1110 | 0 / 4 | pending |
| 817 | Zilan massacre | Crimes | 1110 | 3 / 5 | pending |
| 826 | Tsuyama massacre | Crimes | 1111 | 0 / 4 | pending |
| 288 | 14K (triad) | Groups | 1112 | 2 / 5 | pending |
| 508 | Geoffrey Edelsten | Criminals | 1113 | 0 / 5 | pending |
| 677 | 2008 bombing of Indian embassy in Kabul | Crimes | 1113 | 0 / 4 | pending |
| 757 | Lachine massacre | Crimes | 1113 | 0 / 4 | pending |
| 736 | Battle of Praga | Crimes | 1115 | 0 / 4 | pending |
| 120 | Aslan Byutukayev | Criminals | 1116 | 0 / 6 | pending |
| 849 | Kragujevac massacre | Crimes | 1118 | 3 / 5 | pending |
| 72 | Abu Nabil al-Anbari | Criminals | 1120 | 0 / 6 | pending |
| 324 | Bandidos Motorcycle Club | Groups | 1121 | 0 / 5 | pending |
| 366 | Shantaram (novel) | Criminals | 1121 | 0 / 4 | pending |
| 706 | Sicilian Vespers | Crimes | 1123 | 0 / 4 | pending |
| 232 | Mexican Mafia | Groups | 1125 | 0 / 4 | pending |
| 903 | Paris massacre of 1961 | Crimes | 1125 | 0 / 4 | pending |
| 892 | Hill 303 massacre | Crimes | 1130 | 0 / 4 | pending |
| 417 | Brian Burke (Australian politician) | Criminals | 1132 | 0 / 4 | pending |
| 333 | Loners Motorcycle Club | Groups | 1133 | 2 / 4 | pending |
| 806 | Hanapepe massacre | Crimes | 1133 | 0 / 4 | pending |
| 981 | Bisho massacre | Crimes | 1134 | 0 / 4 | pending |
| 584 | Salvatore Lo Piccolo | Criminals | 1139 | 0 / 5 | pending |
| 1012 | Beslan school siege | Crimes | 1141 | 0 / 5 | pending |
| 672 | 2020 Kabul University attack | Crimes | 1144 | 0 / 5 | pending |
| 915 | Mỹ Lai massacre | Crimes | 1144 | 0 / 5 | pending |
| 709 | Lisbon massacre | Crimes | 1145 | 0 / 4 | pending |
| 771 | Sand Creek massacre | Crimes | 1146 | 0 / 5 | pending |
| 280 | Bamboo Union | Groups | 1150 | 5 / 5 | pending |
| 408 | Carl Williams (criminal) | Criminals | 1150 | 0 / 5 | pending |
| 1134 | Laal Singh Chaddha | Blog | 1151 | 0 / 4 | pending |
| 711 | Ottoman–Venetian War (1570–1573) | Crimes | 1152 | 0 / 4 | pending |
| 409 | Jason Moran (criminal) | Criminals | 1153 | 0 / 4 | pending |
| 809 | Columbine Mine massacre | Crimes | 1154 | 0 / 5 | pending |
| 599 | Antonio Pelle | Criminals | 1156 | 1 / 5 | pending |
| 388 | Jim Krakouer | Criminals | 1157 | 0 / 4 | pending |
| 441 | Tony Mokbel | Criminals | 1158 | 0 / 5 | pending |
| 301 | Organised crime in Pakistan | Groups | 1160 | 0 / 5 | pending |
| 452 | Christopher Dale Flannery | Criminals | 1162 | 0 / 4 | pending |
| 713 | Siege of Smerwick | Crimes | 1162 | 3 / 4 | pending |
| 330 | Hells Angels | Groups | 1164 | 2 / 5 | pending |
| 277 | Tiny Rascal Gang | Groups | 1165 | 5 / 5 | pending |
| 950 | Dos Erres massacre | Crimes | 1165 | 0 / 4 | pending |
| 696 | 2021 Kabul airport attack | Criminals | 1166 | 0 / 5 | pending |
| 471 | Murder of Anita Cobby | Criminals | 1168 | 2 / 5 | pending |
| 113 | Bahrun Naim | Criminals | 1170 | 0 / 6 | pending |
| 430 | Glenn Wheatley | Criminals | 1170 | 0 / 4 | pending |
| 954 | 1984 anti-Sikh riots | Crimes | 1173 | 0 / 4 | pending |
| 798 | Yalova Peninsula massacres | Crimes | 1174 | 0 / 4 | pending |
| 1110 | Highland Park parade shooting | Crimes | 1174 | 0 / 5 | pending |
| 832 | Fântâna Albă massacre | Crimes | 1179 | 4 / 4 | pending |
| 808 | Shanghai massacre | Crimes | 1181 | 0 / 5 | pending |
| 972 | Somaliland War of Independence | Crimes | 1182 | 0 / 4 | pending |
| 493 | Wagga Wagga | Criminals | 1187 | 3 / 4 | pending |
| 977 | Khojaly massacre | Crimes | 1191 | 0 / 4 | pending |
| 456 | Julian Knight (murderer) | Criminals | 1194 | 0 / 5 | pending |
| 812 | Banana Massacre | Crimes | 1196 | 0 / 5 | pending |

## Tier 3: long enough, but sources are missing or homepage-only

| Post | Title | Category | Words | Sources (homepage-only / total) | Status |
|---|---|---|---|---|---|
| 278 | Triad (organized crime) | Groups | 1425 | 4 / 4 | pending |
| 284 | Wah Ching | Groups | 1294 | 5 / 5 | pending |
| 499 | John and Sarah Makin | Criminals | 1234 | 4 / 4 | pending |
| 560 | Buenos Aires | Criminals | 1252 | 4 / 4 | pending |
| 572 | Madrid | Criminals | 1825 | 5 / 5 | pending |
| 574 | Bangkok | Criminals | 1702 | 4 / 4 | pending |
| 577 | Commisso 'ndrina | Criminals | 1668 | 4 / 4 | pending |
| 590 | Lima | Criminals | 1392 | 4 / 4 | pending |
| 787 | Adana massacre | Crimes | 1380 | 5 / 5 | pending |
| 788 | Massacres of Albanians in the Balkan Wars | Crimes | 1946 | 5 / 5 | pending |
| 789 | Ludlow Massacre | Crimes | 1927 | 5 / 5 | pending |
| 838 | Babi Yar | Crimes | 1346 | 4 / 4 | pending |
| 839 | Ninth Fort massacres of November 1941 | Crimes | 1216 | 4 / 4 | pending |
| 840 | Rumbula massacre | Crimes | 1849 | 4 / 4 | pending |
| 1072 | Mongols Motorcycle Club | Groups | 1213 | 5 / 5 | pending |
