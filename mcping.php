<?php
/*

MIT License

Copyright (c) 2021 Leopold Hauptman

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/


function pingServer() {
	//Preparing socket
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) 
	   return json_decode('{"error": "socket_create() failed: reason: ' . socket_strerror(socket_last_error($socket)) . '"}',true);
	//Change ip and port for your needs
	$result = socket_connect($socket, "localhost", 25565);
	if ($result === false) 
		return json_decode('{"error": "socket_connect() failed: reason: '."($result)" . socket_strerror(socket_last_error($socket)) . '"}',true);
	
	//Sending what I believe is server list packet
	$bindata = hex2bin("1000F405096C6F63616C686F737463DD010100");//Hex obtained using MC Client 1.17.1 and Hercules TCP Server
	socket_write($socket, $bindata, 64);
	//Reading data
	$data = '';
	if (false === socket_recv($socket, $data, 5, MSG_WAITALL)) //Waiting and reading first 5 bytes of data
		return json_decode('{"error": "socket_recv() failed: reason: '. socket_strerror(socket_last_error($socket)) . '"}',true);
	
	if (false === socket_recv($socket, $data, 5000, MSG_DONTWAIT)) //Reading the rest of data. Only JSON is needed, so first 5 bytes are ignored.
		return json_decode('{"error": "socket_recv() failed: reason: '. socket_strerror(socket_last_error($socket)) . '"}',true);
	
	socket_close($socket);

	return json_decode($data,true);
}
