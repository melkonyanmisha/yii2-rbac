import React from 'react';
import styles from './button.module.scss';

export const Button = ({
  text,
  onClick,
  disabled = false
}) => {
  return (
    <div className={ styles.layout }>
      <button className={ styles.button } onClick={ onClick } disabled={ disabled }>
        { text }
      </button>
    </div>
  );
}
