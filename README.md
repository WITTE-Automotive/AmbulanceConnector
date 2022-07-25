# Penta Hospitals Connector

This package integrates communication with Penta Hospitals' interface for managing WITTE Automotive's employee examination bookings.  
This extension is used for internal needs of WITTE Nejdek, spol. s r.o., WITTE Access Technology s.r.o. and WITTE Paint Application s.r.o.  


## Installation

The best way to install witte_automotive/penta_hospitals_connector is using Composer:

```bash
composer require witte_automotive/penta_hospitals_connector
```

then you can register extension into DIC:

```yaml
extensions:
  ambulanceConnector: witte_automotive\AmbulanceConnector\Extension
```

## Configuration  
  
```yaml
ambulanceConnector:
  uri: "https://www.nemosnet.cz/webobj-ws/"
  token: <token here>
```