# MIRISK: Mitigation Information and Risk Identification System
MIRISK was developed for the World Bank with assistance from Government of Japan under Japan CTF. Developement was done by the MIRISK Team at Kyoto University (Christakis Mina, Takahiro Tsutsumiuchi), under the direction of Prof. Charles Scawthorn and the World Bank's Global Facility for Disaster Reduction and Recovery (Saroj K. Jha, Program Manager).

## Introduction ##
During the 1990s, the cumulative loss of economic assets due to natural disasters was estimated at 2.5% of 2000 GDP for China, 5.2% for Bangladesh, and 15.6% for Nicaragua. This loss includes network infrastructure (e.g., bridges, power transmission lines, pipelines, etc.) not engineered to withstand the impacts of earthquakes and floods. Most visibly, building infrastructure is vulnerable. The two major earthquakes in Turkey in 1999 damaged some 23,400 buildings. Some 16,400 of these, encompassing 93,000 housing units and 15,000 small business units, collapsed or were heavily damaged. The World Bank has contributed significantly to disaster recovery efforts across the developing world including nearly a billion dollars for Asian tsunami recovery, reconstruction following the Gujarat earthquake in India ($443m), Hurricane Mitch reconstruction in Honduras ($200m), Marmara earthquake reconstruction in Turkey ($505m), and flood recovery in Mozambique ($30m).

The World Bank is increasingly integrating natural hazard risk management and mitigation approaches into the design of infrastructure projects as well as in policy advice to clients to ensure that new infrastructure is able to withstand disaster impacts. Mitigation Information and Risk Identification System (MIRISK) is one such tool which will provide sources of information for natural hazards design guidelines, norms and good practices, and is intended for use by World Bank's Development Managers during all stages of the Project Cycle. MIRISK has been developed to let users identify the natural hazards related to the development project, identify the typical vulnerability of each infrastructure, identify normal design and mitigation plan of each infrastructure.

A Natural Disaster can destroy years of Development in a few seconds.

This is because building design codes are only a minimum level of design. That is, the purpose of normal building design codes is not to eliminate all damage given a major earthquake, flood or tropical cyclone. Rather, the code’s purpose is to prevent major loss of life – significant damage is acceptable per modern building codes, if not many people die.

It can be very wise, and cost-effective, for a Development Manager to require a moderately enhance level of construction for natural hazards for a Project. This is especially true when one considers the total costs of damage, in terms of Project loss of use ("business interruption").

MIRISK is a tool to help Development Managers consider natural hazards risk, and ways to reduce that risk, by:
* identifying natural hazards affecting a region
* defining the kinds of infrastructure ("assets") that make up typical Development projects
* describing the vulnerability of these assets to natural hazards, and how vulnerability can be reduced
* analyzing the natural hazards and vulnerability data, to assess whether Projects should follow normal design practices, or whether the cost of some enhanced design for natural hazards is justified by the benefits (of avoided losses).

*Natural hazards considered in MIRISK are earthquakes, flood, tropical cyclone, and volcanism.*

MIRISK’s basic purpose is to allow a Development Manager to quickly learn if natural hazards are very significant in a region where the Manager is considering development. If so, MIRISK provides information on what can be done, and permits estimation of the added cost for a moderately enhanced level of construction for natural hazards. An ‘optimum’ level of enhanced construction is estimated, based on the degree of hazard, the type of facility, and the Project’s benefit cost ratio (BCR, used to account for indirect costs of damage).

##Requirements##
* LAMP or WAMP stack
* php
* postgresql
* postGIS

##Installation##
* Copy the files to the webserver.
* Unzip databasedump\DB-MIRISK-26JUN2007.zip
* Import the database from the dump
* Unzip map-fu\map\world\gshhs_land.zip

##How to use##
Follow the instructions in *MIRISK-HELP.pdf*

##License##

Copyright Charles Scawthorn, [Christakis Mina](https://github.com/chrmina) and Takahiro Tsutsumiuchi

MIRISK has been developed under and is subject to the terms of the Kyoto University Intellectual Property Policy, available at http://www.kyoto-u.ac.jp/en/about/profile/ideals/documents/e-ipp.pdf.

MIRISK is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.