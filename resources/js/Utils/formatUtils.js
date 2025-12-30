export const getAge = (date, hasText = false) =>
    date ? `${(new Date() - new Date(date)) / (1000 * 60 * 60 * 24 * 365.25) | 0}${hasText ? ' anos' : ''}` : null;

export const getGender = gender => gender === 'M' ? 'Masculino' : gender === 'F' ? 'Feminino' : 'Outros';
