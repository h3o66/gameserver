<?php
/*------------------------------------------------------------------------
# com_gameserver - GameServer!
# ------------------------------------------------------------------------
# author    Lars Hildebrandt
# copyright Copyright (C) 2014 Lars Hildebrandt. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.larshildebrandt.de
# Technical Support:  Forum - http://www..larshildebrandt.de/forum/
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * $Id: ventrilo.php,v 1.4 2010/02/10 14:59:28 evilpie Exp $
 * Copyright (C) 2005 C. Mark Veaudry
 * Copyright 2005 Luigi Auriemma
 */


require_once GAMEQ_BASE . 'Protocol.php';


/**
 * Ventrilo Protocol
 * For newer Servers wich dont require a password for queries (Should be >= 3.0.3)
 * @author      Luigi Auriemma <www.aluigi.org> (Reversing and first version in c)
 * @author      C. Mark Veaudry (PHP Port)
 * @author      Tom Schuster <evilpie@users.sf.net>
 * @version     $Revision: 1.4 $
 */
class GameQ_Protocol_ventrilo extends GameQ_Protocol
{
	private $head_encrypt_table = array(
	0x80, 0xe5, 0x0e, 0x38, 0xba, 0x63, 0x4c, 0x99, 0x88, 0x63, 0x4c, 0xd6, 0x54, 0xb8, 0x65, 0x7e,
	0xbf, 0x8a, 0xf0, 0x17, 0x8a, 0xaa, 0x4d, 0x0f, 0xb7, 0x23, 0x27, 0xf6, 0xeb, 0x12, 0xf8, 0xea,
	0x17, 0xb7, 0xcf, 0x52, 0x57, 0xcb, 0x51, 0xcf, 0x1b, 0x14, 0xfd, 0x6f, 0x84, 0x38, 0xb5, 0x24,
	0x11, 0xcf, 0x7a, 0x75, 0x7a, 0xbb, 0x78, 0x74, 0xdc, 0xbc, 0x42, 0xf0, 0x17, 0x3f, 0x5e, 0xeb,
	0x74, 0x77, 0x04, 0x4e, 0x8c, 0xaf, 0x23, 0xdc, 0x65, 0xdf, 0xa5, 0x65, 0xdd, 0x7d, 0xf4, 0x3c,
	0x4c, 0x95, 0xbd, 0xeb, 0x65, 0x1c, 0xf4, 0x24, 0x5d, 0x82, 0x18, 0xfb, 0x50, 0x86, 0xb8, 0x53,
	0xe0, 0x4e, 0x36, 0x96, 0x1f, 0xb7, 0xcb, 0xaa, 0xaf, 0xea, 0xcb, 0x20, 0x27, 0x30, 0x2a, 0xae,
	0xb9, 0x07, 0x40, 0xdf, 0x12, 0x75, 0xc9, 0x09, 0x82, 0x9c, 0x30, 0x80, 0x5d, 0x8f, 0x0d, 0x09,
	0xa1, 0x64, 0xec, 0x91, 0xd8, 0x8a, 0x50, 0x1f, 0x40, 0x5d, 0xf7, 0x08, 0x2a, 0xf8, 0x60, 0x62,
	0xa0, 0x4a, 0x8b, 0xba, 0x4a, 0x6d, 0x00, 0x0a, 0x93, 0x32, 0x12, 0xe5, 0x07, 0x01, 0x65, 0xf5,
	0xff, 0xe0, 0xae, 0xa7, 0x81, 0xd1, 0xba, 0x25, 0x62, 0x61, 0xb2, 0x85, 0xad, 0x7e, 0x9d, 0x3f,
	0x49, 0x89, 0x26, 0xe5, 0xd5, 0xac, 0x9f, 0x0e, 0xd7, 0x6e, 0x47, 0x94, 0x16, 0x84, 0xc8, 0xff,
	0x44, 0xea, 0x04, 0x40, 0xe0, 0x33, 0x11, 0xa3, 0x5b, 0x1e, 0x82, 0xff, 0x7a, 0x69, 0xe9, 0x2f,
	0xfb, 0xea, 0x9a, 0xc6, 0x7b, 0xdb, 0xb1, 0xff, 0x97, 0x76, 0x56, 0xf3, 0x52, 0xc2, 0x3f, 0x0f,
	0xb6, 0xac, 0x77, 0xc4, 0xbf, 0x59, 0x5e, 0x80, 0x74, 0xbb, 0xf2, 0xde, 0x57, 0x62, 0x4c, 0x1a,
	0xff, 0x95, 0x6d, 0xc7, 0x04, 0xa2, 0x3b, 0xc4, 0x1b, 0x72, 0xc7, 0x6c, 0x82, 0x60, 0xd1, 0x0d );
	
	private $data_encrypt_table = array(
	0x82, 0x8b, 0x7f, 0x68, 0x90, 0xe0, 0x44, 0x09, 0x19, 0x3b, 0x8e, 0x5f, 0xc2, 0x82, 0x38, 0x23,
	0x6d, 0xdb, 0x62, 0x49, 0x52, 0x6e, 0x21, 0xdf, 0x51, 0x6c, 0x76, 0x37, 0x86, 0x50, 0x7d, 0x48,
	0x1f, 0x65, 0xe7, 0x52, 0x6a, 0x88, 0xaa, 0xc1, 0x32, 0x2f, 0xf7, 0x54, 0x4c, 0xaa, 0x6d, 0x7e,
	0x6d, 0xa9, 0x8c, 0x0d, 0x3f, 0xff, 0x6c, 0x09, 0xb3, 0xa5, 0xaf, 0xdf, 0x98, 0x02, 0xb4, 0xbe,
	0x6d, 0x69, 0x0d, 0x42, 0x73, 0xe4, 0x34, 0x50, 0x07, 0x30, 0x79, 0x41, 0x2f, 0x08, 0x3f, 0x42,
	0x73, 0xa7, 0x68, 0xfa, 0xee, 0x88, 0x0e, 0x6e, 0xa4, 0x70, 0x74, 0x22, 0x16, 0xae, 0x3c, 0x81,
	0x14, 0xa1, 0xda, 0x7f, 0xd3, 0x7c, 0x48, 0x7d, 0x3f, 0x46, 0xfb, 0x6d, 0x92, 0x25, 0x17, 0x36,
	0x26, 0xdb, 0xdf, 0x5a, 0x87, 0x91, 0x6f, 0xd6, 0xcd, 0xd4, 0xad, 0x4a, 0x29, 0xdd, 0x7d, 0x59,
	0xbd, 0x15, 0x34, 0x53, 0xb1, 0xd8, 0x50, 0x11, 0x83, 0x79, 0x66, 0x21, 0x9e, 0x87, 0x5b, 0x24,
	0x2f, 0x4f, 0xd7, 0x73, 0x34, 0xa2, 0xf7, 0x09, 0xd5, 0xd9, 0x42, 0x9d, 0xf8, 0x15, 0xdf, 0x0e,
	0x10, 0xcc, 0x05, 0x04, 0x35, 0x81, 0xb2, 0xd5, 0x7a, 0xd2, 0xa0, 0xa5, 0x7b, 0xb8, 0x75, 0xd2,
	0x35, 0x0b, 0x39, 0x8f, 0x1b, 0x44, 0x0e, 0xce, 0x66, 0x87, 0x1b, 0x64, 0xac, 0xe1, 0xca, 0x67,
	0xb4, 0xce, 0x33, 0xdb, 0x89, 0xfe, 0xd8, 0x8e, 0xcd, 0x58, 0x92, 0x41, 0x50, 0x40, 0xcb, 0x08,
	0xe1, 0x15, 0xee, 0xf4, 0x64, 0xfe, 0x1c, 0xee, 0x25, 0xe7, 0x21, 0xe6, 0x6c, 0xc6, 0xa6, 0x2e,
	0x52, 0x23, 0xa7, 0x20, 0xd2, 0xd7, 0x28, 0x07, 0x23, 0x14, 0x24, 0x3d, 0x45, 0xa5, 0xc7, 0x90,
	0xdb, 0x77, 0xdd, 0xea, 0x38, 0x59, 0x89, 0x32, 0xbc, 0x00, 0x3a, 0x6d, 0x61, 0x4e, 0xdb, 0x29 );
	
	public function status()
	{
		$data = $this->p->getData();
		$data = explode ("\n", $data);
		foreach ($data as $line)
		{
			$line = trim ($line);
			$splitat = strpos ($line, ":");
			if ($splitat !== False && $splitat > -1)
			{
				$key = strtolower (substr ($line, 0, $splitat));
				$value = trim(substr ($line, $splitat+1));
				switch ($key)
				{
					case 'client':
						$this->client ($value);
						break;
					case 'channel':
						$this->channel ($value);
						break;
					case 'channelfields': # remove useless fields
					case 'clientfields':
						break;
					default:
						$this->r->add ($key, $this->convertSpecialChars($value));
				}
			}
		}
		
	}
	
	private function convertSpecialChars($data)
	{
		return preg_replace_callback(
		'|%([0-9A-F]{2})|',
			create_function(
				'$matches',
				'return chr (hexdec ($matches[0]));'
			),
        $data
		);
	}
	
	private function channel($data)
	{
		$items = explode (",", $data);
		foreach ($items as $item)
		{
			$temp = explode ("=", $item);
			$key = strtolower ($temp[0]);
			$value = $temp[1];
			$this->r->addTeam ($key, $this->convertSpecialChars($value));
		}
	}

	private function client($data)
	{
		$items = explode (",", $data);
		foreach ($items as $item)
		{
			$temp = explode ("=", $item);
			$key = strtolower ($temp[0]);
			$value = $temp[1];
			$this->r->addPlayer ($key, $this->convertSpecialChars($value));
		}
	}	

	
	public function preprocess($packets)
	{
		$sorted_packets = array ();
		foreach ($packets as $packet)
		{
			# Header :
			$header = substr ($packet, 0, 20);
			$header_items = array ();
			
			$key = array_shift (unpack ("n1", $header));
			$chars = unpack ("C*", substr ($header, 2));
			
			$a1 = $key & 0xFF;
			$a2 = $key >> 8;
			
			if ($a1 == 0)
			{
				throw new GameQ_ParsingException("Header key is invalid");
			}
			
			$table = $this->head_encrypt_table;
			
			
			$key = 0;
			for( $i = 1; $i <= count( $chars ); $i++ ) {
			$chars[$i] -=  ( $table[$a2] + (( $i - 1 ) % 5 )) & 0xFF;
			$a2 = ($a2 + $a1) & 0xFF;
				if ( ( $i % 2 ) == 0 ) {
					$short_array = unpack( "n1", pack( "C2", $chars[$i - 1], $chars[$i] ));
					$header_items[$key] = $short_array[1];
					++$key;
				}
			}
			
			$header_items = array_combine (array ('zero', 'cmd', 'id', 'totlen', 'len', 'totpck', 'pck', 'datakey', 'crc'), $header_items);
			
			
			if ($header_items['totpck'] != count ($packets))
			{
				throw new GameQ_ParsingException("Too less packets recieved");
			}
			
			
			# Data :
			
			$table = $this->data_encrypt_table;

			$a1 = $header_items['datakey'] & 0xFF;
			$a2 = $header_items['datakey'] >> 8;

			if ( $a1 == 0 ) { 
				throw new GameQ_ParsingException("Data key is invalid");
			}

			$chars = unpack( "C*", substr ($packet, 20) );
			$data = "";			
			for( $i = 1; $i <= count( $chars ); $i++ ) {
				$chars[$i] -= ($table[$a2] + (( $i - 1 ) % 72 )) & 0xFF;
				$a2 = ($a2 + $a1) & 0xFF;
				$data .= chr($chars[$i]);
			}
			$sorted_packets[$header_items['pck']] = $data;
			
			#todo: Check CRC ???
			
		}
		return implode ($sorted_packets);
	}
}