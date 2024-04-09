import React from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';

import { useAuth } from './hooks/auth.hook';
import { AuthContext } from './context/AuthContext';

import { Post } from './pages/post';
import { Sign } from './pages/sign';
import { Posts } from './pages/posts';
import { Profile } from './pages/profile';
import { Header } from './components/header';

export const App = () => {
  const { token, login, logout, userId, ready } = useAuth();

  const isAuthenticated = !!token;

  if (!ready) return <div>Loading...</div>;

  return (
    <AuthContext.Provider value={ { token, login, logout, userId, isAuthenticated } }>
      <Header/>

      <Routes>
        <Route path="/" element={ <Posts/> }/>

        { isAuthenticated ?
          <>
            <Route path="/post/:id" element={ <Post/> }/>
            <Route path="/posts/:user" element={ <Posts/> }/>
            <Route path="/user/profile" element={ <Profile/> }/>
          </> :
          <>
            <Route path="/auth/:sign" element={ <Sign/> }/>
          </>
        }

        <Route path="*" element={ <Navigate to="/" replace/> }/>
      </Routes>
    </AuthContext.Provider>
  );
}
