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
| Arguments         | Type    | Critera                                                    |
|-------------------|---------|------------------------------------------------------------|
| emailAddress      | email   | Required value                                             |
| firstName         | string  | Create a new subscriber                                    |
| lastName          | string  | Update a employee to Marketting List                       |
| marketingConsent  | string  | Required value, Must be either "yes" or "no"               |
| dateOfBirth       | date    | Must be of format y-m-d and must be of age 18and above     |
```

### Update a subscriber to Marketting Lists ``` /subscriber/lists```
```
| Arguments         | Type    | Critera                                                    |
|-------------------|---------|------------------------------------------------------------|
| emailAddress      | email   | Required value                                             |
| lists             | string  | Must be comma seperated, if multiple values                |
```


### Create a new enquiry for the subscriber ``` /subscriber/lists```
```
| Arguments         | Type    | Critera                                                    |
|-------------------|---------|------------------------------------------------------------|
| emailAddress      | email   | Required value                                             |
| enquiry           | text    | The characters must not exceed 1000                        |
```
