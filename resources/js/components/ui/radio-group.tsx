import * as React from 'react';
import { cn } from '@/lib/utils';

interface RadioGroupContextValue {
  value?: string;
  onValueChange?: (value: string) => void;
}

const RadioGroupContext = React.createContext<RadioGroupContextValue>({});

interface RadioGroupProps extends React.HTMLAttributes<HTMLDivElement> {
  value?: string;
  onValueChange?: (value: string) => void;
}

const RadioGroup = React.forwardRef<HTMLDivElement, RadioGroupProps>(
  ({ className, children, value, onValueChange, ...props }, ref) => {
    return (
      <RadioGroupContext.Provider value={{ value, onValueChange }}>
        <div
          ref={ref}
          className={cn('grid gap-2', className)}
          role="radiogroup"
          {...props}
        >
          {children}
        </div>
      </RadioGroupContext.Provider>
    );
  }
);
RadioGroup.displayName = 'RadioGroup';

interface RadioGroupItemProps extends React.HTMLAttributes<HTMLDivElement> {
  value: string;
  id?: string;
  disabled?: boolean;
}

const RadioGroupItem = React.forwardRef<HTMLDivElement, RadioGroupItemProps>(
  ({ className, value, id, disabled, children, ...props }, ref) => {
    const { value: selectedValue, onValueChange } = React.useContext(RadioGroupContext);
    const checked = selectedValue === value;
    
    const handleClick = () => {
      if (!disabled && onValueChange) {
        onValueChange(value);
      }
    };
    
    return (
      <div
        ref={ref}
        className={cn(
          'flex items-center space-x-2',
          disabled && 'opacity-50 cursor-not-allowed',
          className
        )}
        onClick={handleClick}
        {...props}
      >
        <div
          className={cn(
            'aspect-square h-4 w-4 rounded-full border',
            checked ? 'border-primary bg-primary' : 'border-input',
            disabled && 'opacity-50'
          )}
          role="radio"
          aria-checked={checked}
          aria-disabled={disabled}
          id={id}
        >
          {checked && (
            <div className="h-2 w-2 translate-x-1 translate-y-1 rounded-full bg-white" />
          )}
        </div>
        {children}
      </div>
    );
  }
);
RadioGroupItem.displayName = 'RadioGroupItem';

export { RadioGroup, RadioGroupItem };
