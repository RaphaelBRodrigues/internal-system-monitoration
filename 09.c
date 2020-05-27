#include <stdio.h>
int main(void){
	int n=0, num=0, i=0, j=0, cRest=0, r=0, QNTprimos=0,primo;
	
	scanf("%d", &n);
	
    for(i=0; i<=n; i++){

    	scanf("%d", &num);
    	    	
        //J tem que ser maior que 1 senão n/1 == 0 ai caga tudo
    	for(j=2; j<num; j++){
            //A comparação tem que ser menor senão n/n == 0 ai caga tudo de novo
         r=num%j;
    	   if(r==0){
               primo = 0;
                break;
			}
          primo = 7;		
        }

        if(primo == 7){
            QNTprimos++;
        }

		}
        //
        printf("Quantidade de primos %d",QNTprimos);
	return 0;
}