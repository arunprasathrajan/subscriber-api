# Subscriber Api

```
## API Endpoints

| Method | Route                     | Description                                  |
|--------|---------------------------|----------------------------------------------|
| GET    | `/`                       | Intial Connection to the Propeller API       |
| POST   | `/subscriber/new`         | Create a new subscriber                      |
| PUT    | `/subscriber/lists`       | Update a employee to Marketting List         |
| POST   | `/subscriber/enquiry`     | Create a new Subscriber enquiry              |
```

### Create a new subscriber ``` /subscriber/new```
```
| Arguments         | Type    | Critera                                                              |
|-------------------|---------|----------------------------------------------------------------------|
| emailAddress      | email   | Required                                                             |
| firstName         | string  | Optional, Characters must not exceed 255                             |
| lastName          | string  | Optional, Characters must not exceed 255                             |
| marketingConsent  | string  | Optional, Must be either "yes" or "no"                               |
| dateOfBirth       | date    | Optional, Must be of format y-m-d and must be of age 18and above     |
```

### Update a subscriber to Marketting Lists ``` /subscriber/lists```
```
| Arguments         | Type    | Critera                                                    |
|-------------------|---------|------------------------------------------------------------|
| emailAddress      | email   | Required                                                   |
| lists             | string  | Required, Must be comma seperated, if multiple values      |
```


### Create a new enquiry for the subscriber ``` /subscriber/lists```
```
| Arguments         | Type    | Critera                                                    |
|-------------------|---------|------------------------------------------------------------|
| emailAddress      | email   | Required                                                   |
| enquiry           | text    | Required, characters must not exceed 1000                  |
```
