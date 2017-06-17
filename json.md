# Tour Planner Module

## Functions currently in the module

* ` Set tour plan details`
    New tour plans will be added through this method.
* ` Get tour plan details `
    The frontend will recieve the tour plan details through this method.
* ` Update status of tour plan `
    The reviewers can see the tour plan and set and update their status through this method.
* ` Change the tour plan `
    The reviewers can also change the tour plan if required through this method.


## Json Schema for the tour plan of a month

```
{
    'plan' : [
                {1:'set-1'},
                {2:'meeting'},
                {3:'leave'},
                .....
                .....
                .....
                {30:'set-4'},
                {31:'meeting'},
            ]
}
```

This JSON object will be serialized and stored in the database. This will be sent by the frontend to the backend. And whenever the frontend requires the tour plan, it will be fetched from the database and unserialized and sent to the frontend.
