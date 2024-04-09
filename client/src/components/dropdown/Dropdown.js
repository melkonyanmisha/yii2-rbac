import React, { useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import styles from './dropdown.module.scss';

import { AuthContext } from '../../context/AuthContext';

export const Dropdown = ({ user, setShow }) => {
  const { logout } = useContext(AuthContext);
  const navigate = useNavigate();

  const onLogout = () => {
    logout();
    navigate('/auth/sign-in');
  }

  return (
    <div className={ styles.layout }>
      <ul className={ styles.links }>
        <li className={ styles.link } onClick={ () => {
          setShow(false);
          navigate(`/user/profile`)
        } }>Profile
        </li>

        <li className={ styles.link } onClick={ () => {
          setShow(false);
          navigate(`/posts/${ user }`)
        } }>My Posts
        </li>

        <li className={ styles.link } onClick={ () => {
          setShow(false);
          onLogout()
        } }>Logout
        </li>
      </ul>
    </div>
  );
}
