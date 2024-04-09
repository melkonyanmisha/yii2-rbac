import React from 'react';
import styles from './input.module.scss';

export const Input = ({
  type,
  name,
  value,
  placeholder,
  onChange,
  disabled = false
}) => {
  return (
    <div className={ styles.layout }>
      <input className={ styles.input }
             type={ type }
             placeholder={ placeholder }
             name={ name }
             value={ value }
             onChange={ onChange }
             disabled={ disabled }/>
    </div>
  );
}
