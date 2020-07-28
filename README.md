# TPTNet-Server
The official source for The Powder Toy Network Server. An open source version of (now defunct) The Powder Toy community server (https://ThePowderToy.Net/).

The source is under Affero GPL which means if you run a modified program on a server and let other users communicate with it there, your server must also allow them to download the source code corresponding to the modified version running there.

## Requirements
- A web server that runs PHP, Python, MySQL, etc.
- Resources to compile a The Powder Toy client on Linux (http://powdertoy.co.uk/Wiki/W/Compiling_TPT%2B%2B_on_debian/ubuntu.html)

## Setup
1. Drop the files into your web server directory where the files can be accessable via a browser like any normal page.

### Compile the Renderer
2. Download (https://github.com/simtr/The-Powder-Toy) the official source for The Powder Toy and drop that into another directory.
3. In the "Renderer" directory, move the "PowderToyRenderer.cpp" file into the "src" directory where place your source.
4. Compile the renderer like you would with the same resources as the client. However when you compile it, use the --renderer flag.
5. Move the renderer to the "Static" directory.

### Static Server
6. Make a subdomain that points to the "Static" directory. This will act like the static server for The Powder Toy.

### Database
7. Upload or execute the included "TPTNet-Server.sql" file to make the databases. The table has comments in order for you to figure out things.
8. Insert your own user details. The hash is simply "md5(USERNAME-md5(PASSWORD))". For security reasons this may change in the future. 

### Client
9. Your own server's client can be made by changing the URLs to the server. Just change the URL directed towards powdertoy.co.uk to your own addresses.
10. Compile the client for whichever operating system you will run the client on.
