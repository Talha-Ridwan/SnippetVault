import React from 'react';
import {Button} from "../components/button";
export default function Dashboard() {
    return (
        <div>
            console.log("This is the dashboard which hasn't been made yet, here's a logout button instead");
            <Button
                size={'large'}
                variant={'primary'}
                id={'logoutButton'}
                >
                Logout
            </Button>
        </div>
    )
}