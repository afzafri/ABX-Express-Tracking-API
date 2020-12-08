# ABX Express Tracking API
Return JSON formatted string of ABX Express Tracking details

# Installation
- Drop all files into your server

# Usage
- ```http://site.com/api.php?trackingNo=CODE```
- where ```CODE``` is your parcel tracking number
- It will then return a JSON formatted string, you can parse the JSON string and do what you want with it.

# Sample Response
```yaml
{
    "http_code": 200,
    "error_msg": "No error",
    "status": 1,
    "message": "Record Found",
    "data": [
        {
            "date": "01/Dec/2020",
            "time": "11:45AM",
            "location": "PKX",
            "process": "ROUTE SCAN"
        },
        {
            "date": "01/Dec/2020",
            "time": "6:54AM",
            "location": "PKX",
            "process": "SHIPMENT ARRIVED AT ABX FACILITY"
        },
        {
            "date": "01/Dec/2020",
            "time": "12:47AM",
            "location": "SUBANG",
            "process": "LOGSHEET"
        },
        {
            "date": "30/Nov/2020",
            "time": "8:23PM",
            "location": "SUBANG",
            "process": "SHIPMENT PICKED UP"
        },
        {
            "date": "30/Nov/2020",
            "time": "8:23PM",
            "location": "SUBANG",
            "process": "STICKER - TOTAL PKG# (VIA HUB). (V1.0.0.42)"
        }
    ],
    "info": {
        "creator": "Afif Zafri (afzafri)",
        "project_page": "https://github.com/afzafri/ABX-Express-Tracking-API",
        "date_updated": "08/12/2020"
    }
}
```

# License
This library is under ```MIT license```, please look at the LICENSE file
