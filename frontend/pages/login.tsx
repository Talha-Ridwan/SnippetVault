import React from 'react';
import {Button} from "../components/button";

export default function LoginPage() {
    return (
        <div>
            <Button 
            variant='primary'
            size='medium'
            onClick={()=>{

            }}
            id={"loginButton"}
            >Sign in or Create Account with Github</Button>
        </div>
    );
}