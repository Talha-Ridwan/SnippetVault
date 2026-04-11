import React from 'react';
import {Button} from "../components/button";

export default function LoginPage() {
    return (
        <div>
            <Button 
            variant='primary'
            size='medium'
            onClick={()=>console.log('Not yet implemented')}
            >Sign in or Create Account with Github</Button>
        </div>
    );
}