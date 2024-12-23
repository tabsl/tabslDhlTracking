# tabslDhlTracking

OXID eShop module for DHL parcel tracking.

- Get parcel tracking information from DHL api and save to database.
- Save delivery date, if parcel is delivered.
- Show parcel events in admin order page.
- Show all parcel information in admin order page.

![tabsldhltracking.jpg](tabsldhltracking.jpg)

## Installation

	composer require tabsl/tabsldhltracking

## Configuration

Add DHL api key (customer key) to the module settings in the admin panel.

## Changelog

    2024-12-23	1.0.2	fix parcel country, check multiple parcel numbers
    2024-12-23	1.0.1	fix namespace path
    2024-12-23	1.0.0	initial release

## License

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

## Copyright

	Tobias Merkl | 2024
	https://oxid-module.eu
