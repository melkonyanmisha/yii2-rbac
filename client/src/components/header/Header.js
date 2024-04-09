import React from 'react';
import styles from './header.module.scss';

import { Navbar } from '../navbar';

export const Header = () => {
  return (
    <div className={ styles.layout }>
      <div className={ styles.container }>
        <Navbar/>
      </div>
    </div>
  );
}
